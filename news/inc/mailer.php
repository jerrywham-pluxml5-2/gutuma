<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The mailer class
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once "swift/Swift.php";
require_once "swift/Swift/Connection/Multi.php";
require_once "swift/Swift/Connection/SMTP.php";
require_once "swift/Swift/Connection/Sendmail.php";
require_once "swift/Swift/Connection/NativeMail.php";
require_once "swift/Swift/Plugin/Decorator.php";
require_once "swift/Swift/Plugin/AntiFlood.php";

/**
 * The sender class
 */
class gu_mailer
{
	private $swift;
	private $from_address;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Create the from address to be used when sending
		$this->from_address = new Swift_Address(gu_config::get('admin_email'), gu_config::get('admin_name'));
	}
	
	/**
	 * Initializes this mailer based on the specified settings or the stored transport settings
	 * @param bool $use_smtp TRUE if sender should use SMTP	 
	 * @param string $smtp_server The SMTP server
	 * @param int $smtp_port The SMTP port
	 * @param string $smtp_encryption The SMTP encryption type
	 * @param string $smtp_username The SMTP username
	 * @param string $smtp_password The SMTP password
	 * @param bool $use_sendmail TRUE if sender should fall back on sendmail if SMTP fails
	 * @param bool $use_phpmail TRUE if sender should fall back on phpmail if SMTP/Sendmail fails	 
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function init($use_smtp = NULL, $smtp_server = NULL, $smtp_port = NULL, $smtp_encryption = NULL, $smtp_username = NULL, $smtp_password = NULL, $use_sendmail = NULL, $use_phpmail = NULL)
	{
		$connections = array();
		
		$use_smtp = isset($use_smtp) ? $use_smtp : gu_config::get('use_smtp');
		$smtp_server = isset($smtp_server) ? $smtp_server : gu_config::get('smtp_server');
		$smtp_port = isset($smtp_port) ? (int)$smtp_port : (int)gu_config::get('smtp_port');
		$smtp_encryption = isset($smtp_encryption) ? $smtp_encryption : gu_config::get('smtp_encryption');
		$smtp_username = isset($smtp_username) ? $smtp_username : gu_config::get('smtp_username');
		$smtp_password = isset($smtp_password) ? $smtp_password : gu_config::get('smtp_password');		
		$use_sendmail = isset($use_sendmail) ? $use_sendmail : gu_config::get('use_sendmail');
		$use_phpmail = isset($use_phpmail) ? $use_phpmail : gu_config::get('use_phpmail');
		
		if (!($use_smtp || $use_sendmail || $use_phpmail))
			return gu_error(t('No method of mail transportation has been configured'));		
		 
		// Add the SMTP connection if details have been given
		if ($use_smtp) {
			switch ($smtp_encryption) {
				case 'SSL': $enc = Swift_Connection_SMTP::ENC_SSL;
				case 'TLS': $enc = Swift_Connection_SMTP::ENC_TLS;
				default: 	$enc = Swift_Connection_SMTP::ENC_OFF;			
			}
			$server = ($smtp_server != '') ? $smtp_server : Swift_Connection_SMTP::AUTO_DETECT;
			$port = ($smtp_port > 0) ? $smtp_port : Swift_Connection_SMTP::AUTO_DETECT;			
				
			$smtp = new Swift_Connection_SMTP($server, $port, $enc);
			if ($smtp_username != '' && $smtp_password != '') {
				$smtp->setUsername($smtp_username);
				$smtp->setPassword($smtp_password);
			}
			$connections[] =& $smtp;
			
			gu_debug(t('Created SMTP connection (%:% Enc:% User:% Pass:%)',array($smtp->getServer(),$smtp->getPort(),$smtp_encryption,$smtp->getUsername(),str_mask($smtp->getPassword()))));	
		}
		
		// Add the SendMail connection option
		if ($use_sendmail)
			$connections[] = new Swift_Connection_Sendmail(Swift_Connection_Sendmail::AUTO_DETECT);
		 
		// Fall back on mail() if all else fails
		if ($use_phpmail)
			$connections[] = new Swift_Connection_NativeMail();
		 
		// And instantiate swift with these connections
		try {
			$this->swift = new Swift(new Swift_Connection_Multi($connections));
		}
		catch (Swift_ConnectionException $e) {
			gu_debug($e->getMessage());
			return gu_error(t("Unable to initialize mailer. Check transport settings."));
		}
		
		// Enable level 3 logging
		$log = Swift_LogContainer::getLog();//Strict standards: Only variables should be assigned by reference
		$log->setLogLevel(3);
	
		return TRUE;
	}
	
	/**
	 * Sends a single email to the specified recipient
	 * @param string $recipient The recipient address
	 * @param string $subject The email subject
	 * @param string $text The email body
	 * @return bool TRUE if mail sent successfully, else FALSE
	 */
	public function send_mail($recipient, $subject, $text)
	{
		$message = new Swift_Message($subject, $text);
		$recipients = new Swift_RecipientList();
		$recipients->addTo($recipient);
			
		return ($this->send($message, $recipients) == 1);
	}
	
	/**
	 * Sends a single email to the administrator email address
	 * @param string $subject The email subject
	 * @param string $text The email body
	 * @return bool TRUE if mail sent successfully, else FALSE
	 */
	public function send_admin_mail($subject, $text)
	{
		return $this->send_mail(gu_config::get('admin_email'), $subject, $text);
	}
	
	/**
	 * Sends the specified newsletter to its recipients
	 * @param string $recipient The recipient address	 
	 * @param gu_newsletter $newsletter The newsletter to send
	 * @param string $list_name The name of the list holding the recipient	 
	 * @return bool TRUE if newsletter sent successfully, -1 if recipient failed, else FALSE 
	 */
	public function send_newsletter($recipient, gu_newsletter $newsletter, $list_name = NULL)
	{
		$message = $this->create_message($newsletter, $recipient, $list_name);
		$recipients = new Swift_RecipientList();
		$recipients->addTo($recipient);
		
		$res = $this->send($message, $recipients);
		return $res === 0 ? -1 : $res;
	}
	
	/**
	 * Sends a message using the Swift mailer
	 * @param Swift_Message $message The Swift message object
	 * @param Swift_RecipientList $recipients The recipient list
	 * @return int The number of messages sent successfully, else FALSE
	 */
	private function send(Swift_Message $message, Swift_RecipientList $recipients)
	{
		if (gu_is_demo())
			return gu_error(t('Unable to send message in demo mode'));
					
		try {
			$num_sent = $this->swift->send($message, $recipients, $this->from_address);
		}
		catch (Swift_ConnectionException $e) {
			gu_debug($e->getMessage());
			return gu_error(t('Unable to send message due to connection error'));
		}
			
		if (gu_is_debugging()) {
			$log = Swift_LogContainer::getLog();//Strict standards: Only variables should be assigned by reference
			
			gu_debug('gu_mailer::send(...)<br />'.nl2br(htmlspecialchars($log->dump(true))).' => '.$num_sent);
			$log->clear();
		}
					
		return $num_sent;
	}
	
	/**
	 * Creates a Swift message from a Gutuma newsletter
	 * @param gu_newsletter $newsletter The newsletter
	 * @param string $recipient The recipient address
	 * @param string $list_name The name of the list holding the recipient
	 * @return mixed The Swift message if successful, else FALSE
	 */
	private function create_message(gu_newsletter $newsletter, $address, $list_name)
	{	
		if (!gu_config::get('msg_prefix_subject'))
			$subject = $newsletter->get_subject();
		elseif ($list_name != '')
			$subject = '['.$list_name.'] '.$newsletter->get_subject();
		else
			$subject = '['.gu_config::get('collective_name').'] '.$newsletter->get_subject();			
		
		if ($list_name != '' && gu_config::get('msg_append_signature')) {
			$text = $newsletter->get_text()."\n-------------------------------------------------\n".t('Unsubscribe').": ".absolute_url('subscribe.php')."?addr=".$address."\n".t('Powered by Gutuma')." (".GUTUMA_URL.")\n";
			$html = $newsletter->get_html().'<hr /><p><a href="'.absolute_url('subscribe.php').'?addr='.$address.'">'.t('Unsubscribe').'</a> '.t('from this newsletter.').t(' Powered by').' <a href="'.GUTUMA_URL.'">'.t('Gutuma').'</a></p>';
		}
		else {
			$text = $newsletter->get_text();//Strict standards: Only variables should be assigned by reference
			$html = $newsletter->get_html();//Strict standards: Only variables should be assigned by reference
		}
		
		// Add text and html as separate MIME parts
		$message = new Swift_Message($subject);	
		$message->attach(new Swift_Message_Part($text));
		$message->attach(new Swift_Message_Part($html, "text/html"));
			
		// Add message attachments
		foreach ($newsletter->get_attachments() as $attachment) {
			if (!$message->attach(new Swift_Message_Attachment(new Swift_File($attachment['path']), $attachment['name'])))
				return gu_error(t("Unable to attach '").$attachment['name']."'");
		}
		
		return $message;
	}
	
	/**
	 * Disconnects this mailer
	 */
	public function disconnect()
	{
		$this->swift->disconnect();
	}
}

?>