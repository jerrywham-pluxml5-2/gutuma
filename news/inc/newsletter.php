<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The newsletter class
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 2.2.1
 * @date	16/07/2020
 * @author	Cyril MAGUIRE, Thomas Ingles
*/
define('FILE_MARKER', "<?php die(); ?>\n");
define('MESSAGE_FILE', 'msg.php');
define('RECIPIENTS_FILE', 'recips.php');
define('LOCK_FILE', "send.lock");
define('ERROR_EXTRA', t('Check permissions for directory <code>%</code>',array(GUTUMA_TEMP_DIR)));
/**
 * The newsletter class
 */
class gu_newsletter{
	private $id;
	private $recipients;
	private $subject;
	private $html;
	private $text;
	private $send_progress;
	private $lock;
	/**
	 * Constructor - creates a new empty newsletter
	 */
	public function __construct(){
		$this->id = time();
		$this->recipients = '';
		$this->subject = '';
		$this->html = '';
		$this->text = '';
		$this->send_progress = array(0,0);
	}
	/**
	 * Gets the ID
	 * @return int The ID
	 */
	public function get_id(){
		return $this->id;
	}
	/**
	 * Sets the ID
	 * @param int $id The ID
	 */
	public function set_id($id){
		$this->id = (int)$id;
	}
	/**
	 * Gets the recipient list
	 * @return string The recipient list
	 */
	public function get_recipients(){
		return $this->recipients;
	}
	/**
	 * Sets the recipient list
	 * @param string $recipients The recipient list
	 */
	public function set_recipients($recipients){
		$this->recipients = $recipients;
	}
	/**
	 * Gets the subject
	 * @return string The subject
	 */
	public function get_subject(){
		return $this->subject;
	}
	/**
	 * Sets the subject
	 * @param string $subject The subject
	 */
	public function set_subject($subject){
		$this->subject = $subject;
	}
	/**
	 * Gets the html part of the content
	 * @return string The html
	 */
	public function get_html(){
		return $this->html;
	}
	/**
	 * Sets the html part of the content
	 * @param string $subject The html
	 */
	public function set_html($html){
		$this->html = $html;
	}
	/**
	 * Gets the text part of the content
	 * @return string The text
	 */
	public function get_text(){
		return $this->text;
	}
	/**
	 * Sets the text part of the content
	 * @param string $subject The text
	 */
	public function set_text($text){
		$this->text = $text;
	}
	/**
	 * Generates the text part of the content automatically from the html part
	 */
	public function generate_text(){
		$this->text = html_to_text($this->html);
	}
	/**
	 * Gets the sending state
	 * @return bool TRUE if this newsletter is being sent, else FALSE
	 */
	public function is_sending(){
		return !empty($this->send_progress[0]);
	}
	/**
	 * Gets the sending progress
	 * @return bool TRUE if this newsletter is being sent, else FALSE
	 */
	public function get_send_progress(){
		return $this->send_progress;
	}
	/**
	 * Get the last sended date #since 2.2.1
	 * @return Text date if have sended one time, else Never
	 */
	public function get_sended_date(){
		$s = $this->get_dir().'/'.LOCK_FILE;
		return (file_exists($s)) ? date (t('Y-m-d H:i'), filemtime($s)) : t('Never');
	}
	/**
	 * Get the first created date #since 2.2.1
	 * @return Text date of created time
	 */
	public function get_created_date(){
		return date (t('Y-m-d H:i'), filemtime($this->get_dir().'/index.html'));
	}
	/**
	 * Get the last modified date #since 2.2.1
	 * @return Text date of msg modified time
	 */
	public function get_msg_date(){
		return date (t('Y-m-d H:i'), filemtime($this->get_dir().'/msg.php'));
	}
	/**
	 * Gets the unique folder associated with this newsletter
	 * @return string The temp folder path
	 */
	public function get_dir(){
		return realpath(GUTUMA_TEMP_DIR).'/'.$this->id;
	}
	/**
	 * Saves this newsletter
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function save(){
		if (gu_is_demo())
			return gu_error('<br />'.t('Newsletters cannot be saved or sent in demo mode'));
		$dir = $this->get_dir();
		if (!file_exists($dir)){# Create newsletter's temp directory if it doesn't already exist
			mkdir($dir);
			if(!file_exists($dir.'/index.html')){#used for find 1st created date #since 2.2.1
				touch($dir.'/index.html');
			}
			mkdir($dir.'/attachments');
		}
		$fh = @fopen($dir.'/'.MESSAGE_FILE, 'w');# Save message file
		if ($fh == FALSE)
			return gu_error('<br />'.t('Unable to save newsletter draft'), ERROR_EXTRA);
		fwrite($fh, FILE_MARKER);
		fwrite($fh, $this->recipients."\n");
		fwrite($fh, $this->subject."\n");
		fwrite($fh, $this->html."\n");
		fwrite($fh, FILE_MARKER);
		fwrite($fh, $this->text."\n");
		fclose($fh);
		return TRUE;
	}
	/**
	 * Replace Outbox newsletter in Drafts #since 2.2.1
	 */
	public function send_to_draft(){
		gu_debug('Replace Outbox newsletter in Drafts : ' . $this->id);
		$this->acquire_lock();
		$dir = $this->get_dir();
		// Newsletter may have been deleted by the process that blocked this process, or may not be ready for sending
		if (!file_exists($dir.'/'.RECIPIENTS_FILE)){
			$this->release_lock();
			return TRUE;
		}
		@unlink($dir.'/'.RECIPIENTS_FILE);// Delete recipients file so when we unlock, waiting processes will detect its gone and not try sending
		$this->release_lock();// Wakeup waiting processes
	}

	/**
	 * Mark this newsletter is sended #since 2.2.1
	 */
	private function sended(){
		gu_debug(t('Mark sended file (%)',array($this->id)));
		@touch($this->get_dir().'/'.LOCK_FILE);
	}
	/**
	 * Acquire a lock on this newsletter - i.e., blocks if another process has aquired it
	 */
	private function acquire_lock(){
		gu_debug(t('Locking recipient file (%)',array($this->id)));
		if(!file_exists($this->get_dir().'/'.LOCK_FILE)){# Used for find last sended date #since 2.2.1
			file_put_contents($this->get_dir().'/'.LOCK_FILE, FILE_MARKER);# Create lock file #old: as in save before "return"
		}
		$this->lock = @fopen($this->get_dir().'/'.LOCK_FILE, 'w');
		if (!$this->lock || !flock($this->lock, LOCK_EX))// | LOCK_NB ::: free.fr fix? no
			return gu_error('<br />'.t('Unable to lock newsletter'));
	}
	/**
	 * Release a lock on this newsletter
	 */
	private function release_lock(){
		gu_debug('<br />'.t('Unlocking recipient file (%)',array($this->id)));
		flock($this->lock, LOCK_UN);
		fclose($this->lock);
	}
	/**
	 * Prepares newsletter for sending
	 * @return TRUE if operation was successful, else FALSE
	 */
	public function send_prepare(){
		if (!$this->save())// Save message to ensure message directory is created
			return FALSE;
		// Parse recipient list into addresses and list names
		$addresses = $this->parse_recipients();
		$num_addresses = count($addresses);
		$this->acquire_lock();
		$dir = $this->get_dir();
		if (!file_exists($dir.'/'.RECIPIENTS_FILE)){// Save address list
			$fh = @fopen($dir.'/'.RECIPIENTS_FILE, 'w');
			if ($fh == FALSE)
				return gu_error('<br />'.t('Unable to save newsletter recipient list'), ERROR_EXTRA);
			$this->send_progress = array(intval($num_addresses), intval($num_addresses));
			fwrite($fh, FILE_MARKER);
			fwrite($fh, $this->send_progress[0].'|'.$this->send_progress[1]."\n");
			foreach (array_keys($addresses) as $addr)
				fwrite($fh, $addr.'|'.$addresses[$addr]."\n");
			fclose($fh);
		}
		$this->release_lock();
		return TRUE;
	}
	/**
	 * Newsletters often can't be sent to all recipients in one batch, so this function
	 * picks up where it left off last, and sends as much as permitted by the batch settings.
	 * @param gu_mailer $mailer The mailer to use to send
	 * @param int $init_start_time If this isn't the first call to send_batch in this script execution
	 *   then this should be the start time of the first call, else NULL
	 * @return TRUE if operation was successful, else FALSE
	 */
	public function send_batch(gu_mailer $mailer, $init_start_time = NULL){
		$this->acquire_lock();
		$dir = $this->get_dir();
		// Newsletter may have been deleted by the process that blocked this process, or may not be ready for sending
		if (!file_exists($dir.'/'.RECIPIENTS_FILE)){
			$this->release_lock();
			return TRUE;
		}
		$fh = @fopen($dir.'/'.RECIPIENTS_FILE, 'r+');// Open recipient list file
		if ($fh == FALSE)
			return gu_error('<br />'.t('Unable to open newsletter recipient file'), ERROR_EXTRA);
		try {//free.fr fix
			@flock($fh, LOCK_EX | LOCK_NB);
		} catch (Exception $e) {
			return gu_error('<br />'.t('Unable to lock newsletter recipient list'). ' :<br />' . $e->getMessage(), ERROR_EXTRA);
		}
/*
		if (!flock($fh, LOCK_EX | LOCK_NB)){//free.fr fix test no
			flock( $fp, LOCK_UN );// release the lock
			fclose($fh);
			$fh = @fopen($dir.'/'.RECIPIENTS_FILE, 'r+');// Re Open recipient list file

			if (!flock($fh, LOCK_EX | LOCK_NB))//free.fr fix
				return gu_error('<br />'.t('Unable to lock newsletter recipient list'), ERROR_EXTRA);
		}
*/
		fgets($fh); // Read file marker
		$header = explode('|', fgets($fh)); // Read header
		$remaining = $header[0];
		$total = $header[1];
		// Start the timer - use the passed start time value if there was one
		$start_time = isset($init_start_time) ? $init_start_time : time();
		// Collect failed recipients
		$remaining_recipients = $failed_recipients = array();
		$total_sent = 0;
		while (!feof($fh)){// Start sending to recipients
			$line = trim(fgets($fh));
			if (strlen($line) == 0)
				break;
			$tokens = explode('|', $line);
			$address = $tokens[0];
			$list = $tokens[1];
			$res = $mailer->send_newsletter($address, $this, $list);
			if ($res === FALSE){
				return FALSE;
			}elseif ($res === -1){
				$failed_recipients[] = $tokens;#tokens is array($address,$list); #old $address.($list != '' ? (' ('.$list.')') : '');
			}else{
				$total_sent = $total_sent + $res;#++;
			}
			if (((time() - $start_time) > (int)gu_config::get('batch_time_limit')) || ($total_sent >= gu_config::get('batch_max_size')))
				break;
		}
		while (!feof($fh)){// Read remaining recipients
			$line = trim(fgets($fh));
			if (strlen($line) > 0)
				$remaining_recipients[] = explode('|', $line);
		}
		// Update recipient list file
		$remain_count = count($remaining_recipients);
		$restore_fails = gu_config::get('batch_never_fail');
		fseek($fh, 0);
		ftruncate($fh, 0);
		fwrite($fh, FILE_MARKER);

		if ($restore_fails)# Restore fail emails to recip.php v2.2.1
			$remain_count = $remain_count + count($failed_recipients);

		$this->send_progress = array(intval($remain_count), intval($total));

		fwrite($fh, $remain_count.'|'.$total);
		foreach ($remaining_recipients as $recip)
			fwrite($fh, implode('|', $recip)."\n");
		if ($restore_fails)# Restore fail emails to recip.php v2.2.1
			foreach ($failed_recipients as $recip)# save|del failed_recipients at end of file recip batch
				fwrite($fh, implode('|', $recip)."\n");
		fclose($fh);
		if ($remain_count == 0){
			@unlink($dir.'/'.RECIPIENTS_FILE);// Delete recipients file so when we unlock, waiting processes will detect its gone and not try sending
			$this->release_lock();// Wakeup waiting processes
			//~ $this->send_progress = NULL;
			if (gu_config::get('batch_to_drafts') )#Return to Draft v2.2.1
				$this->sended();# touch lock file at end of batch
			else #delete @ end
				if (!$this->delete())#old sys
					return FALSE;
		}
		else
			$this->release_lock();

		if (count($failed_recipients) > 0){
			$extra = array();
			foreach ($failed_recipients as $recip)#
				$extra[] = $recip[0].($recip[1] != '' ? (' ('.$recip[1].')') : '');
			if ($restore_fails)# Restore fail emails to recip.php Notice v2.2.1
				$extra[] = t('Recipients are moved at end of list to attempt a new send on next round,<br /><b>Be careful!</b> If have error always with same emails or on same times. Remove bad addresses or send newsletters when server have good disponibilities (morning, night, lunch times, ...)').'.';
			$extra = t('Unable to deliver to:<br /><br />').implode('<br />', $extra);#BAF TRADS & ADD IF $restore_fails : Ceux-ci sont placé en fin de liste du batch
			return gu_error('<br />'.t('Message could not be sent to all recipients'), $extra);
		}
		return TRUE;
	}
	/**
	 * Stores the specified file in this newsletter's directory as an attachment
	 * @param string $path The path of file to add as an attachment
	 * @param string $filename The name of the file
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function store_attachment($path, $filename){
		if (!$this->save())// Save message to ensure message directory is created
			return FALSE;
		$dest_path = $this->get_dir().'/attachments/'.$filename;
		gu_debug(t('Storing attachment') .' '. $dest_path);
		// Move uploaded file to the newsletter's temp directory
		if (!@move_uploaded_file($path, $dest_path))
			return gu_error('<br />'.t('Unable to save uploaded file. Check permissions for directory <code>%</code>',array(GUTUMA_TEMP_DIR)));
		return TRUE;
	}
	/**
	 * Deletes the specified file from this newsletter's directory
	 * @param string $filename The name of the file to delete
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function delete_attachment($filename){
		if (!@unlink($this->get_dir().'/attachments/'.$filename))// Delete file from newsletter's temp directory
			return gu_error('<br />'.t('Unable to delete uploaded file. Check permissions for directory <code>%</code>',array(GUTUMA_TEMP_DIR)));
		if (!$this->save())// Save the message
			return FALSE;
		return TRUE;
	}
	/**
	 * Gets the attachments stored for this newsletter
	 * @return array An array of file paths
	 */
	public function get_attachments(){
		$dir = $this->get_dir();
		if (!file_exists($dir))
			return array();
		$files = array();
		if ($dh = @opendir($dir.'/attachments')){
			while (($file = readdir($dh)) !== FALSE){
				if (!is_dir($file)){
					$path = $dir.'/attachments/'.$file;
					$files[] = array('name' => $file, 'path' => $path, 'size' => filesize($path));
				}
			}
			closedir($dh);
		}
		return $files;
	}
	/**
	 * Parses this newsletter's recipient list into an array of addresss and list names
	 * @return array The array of email addresses and list names
	 */
	public function parse_recipients(){
		$list_names = array();
		$addresses = array();
		$items = explode(';', $this->recipients);
		foreach ($items as $r){
			$recip = trim($r);
			if (strlen($recip) == 0)
				continue;
			elseif (strpos($recip, '@') === FALSE)// If token contains a @ then its an email address, otherwise its list
				$list_names[] = $recip;
			else
				$addresses[$recip] = '';
		}
		// Add addresses from each list, in reverse order, so that duplicates for addresses on more than one list, come from the first occuring lists
		for ($l = (count($list_names) - 1); $l >= 0; $l--){
			if ($list = gu_list::get_by_name($list_names[$l], TRUE)){
				foreach ($list->get_addresses() as $address)
					$addresses[$address] = $list->get_friend();#$list->get_name();
			}
			else
				return gu_error('<br />'.t('Unrecognized list name <i>%</i>',array($list_names[$l])));
		}
		// If admin wants a copy, add the admin address as well
		if (gu_config::get('msg_admin_copy'))
			$addresses[gu_config::get('admin_email')] = '';
		return $addresses;
	}
	/**
	 * Cleans up any resources used by this newsletter - i.e. deletes file attachments from temp storage
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function delete(){
		gu_debug(t('Deleting newsletter files (%)',array($this->id)));
		$dir = $this->get_dir();
		if (!file_exists($dir))
			return TRUE;
		foreach ($this->get_attachments() as $attachment){// Delete individual attachments to ensure directory is empty
			if (!$this->delete_attachment($attachment['name']))
				return gu_error('<br />'.t('Unable to delete message attachment'), ERROR_EXTRA);
		}
		// Delete the newsletter files
		$res1 = @rmdir($dir.'/attachments');// (effacement normal ailleurs que chez Free)
		if (is_dir($dir.'/attachments')) {// l'effacement a échoué
			$res1 = rename($dir.'/attachments',$dir.'/../../.trash_me');// rename "spécial Free" rename empty folders to .trash_me (effet de bord non garanti de rename)
		}
		$res2 = @unlink($dir.'/'.MESSAGE_FILE);
		$res3 = !file_exists($dir.'/'.LOCK_FILE) || @unlink($dir.'/'.LOCK_FILE);
		$res4 = !file_exists($dir.'/'.RECIPIENTS_FILE) || @unlink($dir.'/'.RECIPIENTS_FILE);
		$res5 = @unlink($dir.'/index.html');
		$res6 = @rmdir($dir);
		if (is_dir($dir)) {// l'effacement a échoué
			$res6 = rename($dir,$dir.'/../../.trash_me');// rename "spécial Free" rename empty folders to .trash_me (effet de bord non garanti de rename)
		}
		if (!($res1 && $res2 && $res3 && $res4 && $res5))
			return gu_error('<br />'.t('Some newsletter files could not be deleted') . ' ::: WIP '.$res1  . ' : ' . $res2  . ' : ' . $res3  . ' : ' . $res4  . ' : ' . $res5, ERROR_EXTRA);
		$this->send_progress = NULL;
		return TRUE;
	}
	/**
	 * Gets a newsletter
	 * @param int $id The id of the newsletter to retrieve
	 * @return mixed The newsletter if it was loaded successfully, else FALSE if an error occured
	 */
	public static function get($id){
		$h = @fopen(realpath(GUTUMA_TEMP_DIR.'/'.$id.'/'.MESSAGE_FILE), 'r');// Open message file
		if ($h == FALSE)
			return gu_error('<br />'.t("Unable to open message file").' : '.GUTUMA_TEMP_DIR.'/'.$id.'/'.MESSAGE_FILE);
		fgets($h); // Discard first line
		$newsletter = new gu_newsletter();
		$newsletter->id = $id;
		$newsletter->recipients = fgets($h);
		$newsletter->subject = fgets($h);
		while (!feof($h)){// Read message HTML up to marker
			$line = fgets($h);
			if ($line == FILE_MARKER)
				break;
			else
				$newsletter->html .= $line;
		}
		while (!feof($h))// Read message TEXT as rest of file
			$newsletter->text .= fgets($h);
		fclose($h);
		// Check for recips file which means its being sent
		$recip_file = GUTUMA_TEMP_DIR.'/'.$id.'/'.RECIPIENTS_FILE;
		if (file_exists($recip_file)){
			$rh = @fopen(realpath($recip_file), 'r');// Open list file
			if ($rh == FALSE)
				return gu_error('<br />'.t("Unable to read newsletter recipient file"));
			fgets($rh); // Read file marker line
			$header = explode("|", fgets($rh));
			$newsletter->send_progress = array(intval($header[0]), intval($header[1]));
			fclose($rh);
		}
		return $newsletter;
	}
	/**
	 * Gets all the newsletters
	 * @return array The newsletters
	 */
	public static function get_all(){
		$newsletters = array();
		if ($dh = @opendir(realpath(GUTUMA_TEMP_DIR))){
			while (($f = readdir($dh)) !== FALSE){
				if (strpos($f,'.') !== FALSE)//. OR .. OR ##timeStamp##.php OR index(.)html OR .htaccess
					continue;
				if (($newsletter = self::get($f)) !== FALSE)
					$newsletters[] = $newsletter;
			}
		}
		else
			return gu_error('<br />'.t('Unable to open newsletter folder'), ERROR_EXTRA);
		return $newsletters;
	}
	/**
	 * Gets all the newsletters, organized into a mailbox
	 * @return array The newsletters as a mailbox with top level indexes of drafts and outbox
	 */
	public static function get_mailbox(){
		$mailbox = array('drafts' => array(), 'outbox' => array());
		if (($newsletters = self::get_all()) === FALSE)
			return FALSE;
		foreach ($newsletters as $newsletter){
			if ($newsletter->is_sending())
				$mailbox['outbox'][] = $newsletter;
			else
				$mailbox['drafts'][] = $newsletter;
		}
		return $mailbox;
	}
}