<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The address list class and functions
 * @modifications Cyril Maguire, Thomas Ingles
 *
 * Gutama plugin package
 *  @version 2.0.0
 * @date	23/09/2018
 * @author	Cyril MAGUIRE, Thomas Ingles
*/
/**
 * Address list class
 */
class gu_list{
	private $id;
	private $name;
	private $private;
	private $addresses;
	private $size;
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
	 * Gets the name
	 * @return string The name
	 */
	public function get_name(){
		return $this->name;
	}
	/**
	 * Sets the name
	 * @param string $name The name
	 */
	public function set_name($name){
		$this->name = $name;
	}
	/**
	 * Gets the privacy status
	 * @return bool The privacy status
	 */
	public function is_private(){
		return $this->private;
	}
	/**
	 * Sets the privacy status
	 * @param bool $private The privacy status
	 */
	public function set_private($private){
		$this->private = $private;
	}
	/**
	 * Gets the addresses
	 * @return array The addresses
	 */
	public function get_addresses(){
		return $this->addresses;
	}
	/**
	 * Gets the number of addresses in this list
	 * @return The number of addresses
	 */
	public function get_size(){
		return isset($this->addresses) ? count($this->addresses) : $this->size;
	}
	/**V2.0.0
	 * Get addresse in this io list ($this->contains() callback)
	 */
	public static function get_tmp_address(&$address){
		$a = explode(';',$address);//#time#;em@i.l
		$address = array($a[1],$a[0]);//mail,time
	}
	/**
	 * Checks to see if this list contains the given address
	 * @param string $address The address to look for
	 * @param bool $tmp If is a transition list
	 * @param string $k key code (if 0000 return key code) : $this->timeAddress contain timestamp
	 * @return bool TRUE if the list contains the address
	 */
	public function contains($address, $tmp = '', $k = ''){
		if($tmp){
			$addressesStr = $this->addresses;
			array_walk($addressesStr, array('self', 'get_tmp_address'));
			$addresses = array();
			foreach($addressesStr as $key => $val){
				$addresses[] = $val[0];#mail only
				if($k AND $address == $val[0]){#hash & same mail
					$this->timeAddress = $val[1];//For calculate key code & display date time
					if($k=='0000')
						$res = TRUE;//For return key code
					else
						$res = ($k == $this->get_tmp_key($val[0],$val[1]));
					return $res;
				}
			}
			return in_array($address, $addresses);
		}else{
			return in_array($address, $this->addresses);
		}
	}
	/**V2.0.0
	 * generate Hash Key of temporary address
	 * @param string $address
	 * @param string $time
	 * @return The hash of address OR FALSE
	 */
	public function get_tmp_key($address = '', $time = ''){//bep...
/* MAYBE FOUND TIME IN TEMPORARY LISTS */
		if($address&&!$time){
			if($this->contains($address,TRUE,'0000'))
				$time = $this->timeAddress;
		}
		return $address&&$time ? sha1(md5($time).$address) : FALSE;
	}
	/**
	 * Adds the specified address to this list
	 * @param string $address The address to add
	 * @param bool $update TRUE if list should be updated, else FALSE
	 * @param bool $tmp if is a transition list
	 * @param str $k key HASH
	 * @return bool TRUE if the address was successfully added
	 */
	public function add($address, $update, $tmp = '', $k = ''){
		if ($update){
			if ($this->contains($address))
				return gu_error('<br />'.t('Address <b><i>%</i></b> already in the % list of <b><i>%</i></b>',array($address,($tmp?t('temporary'):t('real')), $this->name)));//in the transition list
			if (strlen($address) > GUTUMA_MAX_ADDRESS_LEN)
				return gu_error('<br />'.t('Addresses cannot be more than % characters',array(GUTUMA_MAX_ADDRESS_LEN)));
			if (gu_is_demo() && count($this->addresses) >= GUTUMA_DEMO_MAX_LIST_SIZE)
				return gu_error('<br />'.t('Lists can have a maximum of % addresses in demo mode',array(GUTUMA_DEMO_MAX_LIST_SIZE)));
			$this->addresses[] = ($k?'':time().';').$address;//bep si tmp add #as#;my@em.ail
			natcasesort($this->addresses);
			if (!$this->update($tmp))
				return FALSE;
		}
		return TRUE;
	}
	/**
	 * Removes the specified address from this list
	 * @param string $address The address to remove
	 * @param bool $update TRUE if list should be updated, else FALSE
	 * @param string $tmp : empty | i : list ²Opt IN&OUT²
	 * @param string $k empty | HASHkey
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function remove($address, $update, $tmp = '', $k = ''){
// Create new address array minus the one being removed
		$found = array(false,'');
		$newaddresses = array();
		foreach ($this->addresses as $a){
			if ($tmp){
				$b = explode(';',$a);
				$a = $b[1];
			}
			if ($address != $a)
				$newaddresses[] = ($tmp?$b[0].';':'').$a;
			else
				$found = array(true,($k?'':time().';').$a);//if tmp add ! #timestamp#;my@em.ail
		}
		if (!$found[0])
			return gu_error('<br />'.t('Address <b><i>%</i></b> not found in the % list <b><i>%</i></b>',array($address,($tmp?t('temporary'):t('real')),$this->name)));
		$this->addresses = $newaddresses;
		if ($update){
			if (!$this->update($tmp))
				return FALSE;
		}
		return TRUE;
	}
	/**
	 * Returns a set of addresses from this list
	 * @param string $filter The pattern to look for in each address
	 * @param int $start The offset of the first filtered address to be returned
	 * @param int $count The number of filtered addresses to return
	 * @param int $filtered_total Receives the original count of filtered addresses
	 * @return array An array containing the addresses
	 */
	public function select_addresses($filter, $start, $count, &$filtered_total, $reverse=FALSE){
		$time_start = microtime();
		if ($filter == '')
			$addresses =& $this->addresses;
		else {
			$addresses = array();
			foreach ($this->addresses as $a){
				if (strstr($a, $filter) !== FALSE)
					$addresses[] = $a;
			}
		}
		$filtered_total = count($addresses);
		gu_debug('gu_list::select_addresses("'.$filter.'", '.$start.', '.$count.') '.(microtime() - $time_start).' secs');
		if($reverse) $addresses = array_reverse($addresses);
		return array_slice($addresses, ($start), $count);
	}
	/**
	 * Updates this address list, i.e., saves any changes
	 * @param string $tmp : empty | i : list ²Opt IN&OUT²
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function update($tmp = ''){
		$dr = $tmp?GUTUMA_TEMP_DIR:GUTUMA_LISTS_DIR;
		$lh = @fopen(realpath($dr).'/'.$this->id.($tmp?'.'.$tmp:'').'.php', 'w');
		if ($lh == FALSE)
			return gu_error('<br />'.t('Unable to write list file. Check permissions for directory <code>%</code>',array($dr)));
		fwrite($lh, "<?php die(); ?>".$this->id.'|'.$this->name.'|'.($this->private ? '1' : '0').'|'.count($this->addresses)."\n");
		if(is_array($this->addresses))#update (install.php) Fix
			foreach ($this->addresses as $a)#                                       \/if unsubscribe
				fwrite($lh, $a."\n");#if [1st step?] (tmp) add SALT&Hash :ou:ailleur (add&remove):  ! my@em.ail;SALT&Hash
		fclose($lh);
		return TRUE;
	}
	/**
	 * Deletes this address list : remove 3 lists (real & tmp IO)
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function delete(){
		foreach(array('','i'/*,'o'*/) as $tmp){
			$dr = $tmp?GUTUMA_TEMP_DIR:GUTUMA_LISTS_DIR;
			if (!@unlink(realpath($dr.'/'.$this->id.($tmp?'.'.$tmp:'').'.php')))
				return gu_error('<br />'.t('Unable to delete list. Check permissions for directory <code>%</code>',array($dr)));
		}
				return TRUE;
	}
	/**
	 * Gets the list with the specified id
	 * @param int $id The list id
	 * @param bool $load_addresses TRUE is list addresses should be loaded (default FALSE)
	 * @param str $tmp : empty | i ???| o
	 * @return mixed The list or FALSE if an error occured
	 */
	public static function get($id, $load_addresses = FALSE, $tmp = ''){
		$time_start = microtime();
		$dr = $tmp?GUTUMA_TEMP_DIR:GUTUMA_LISTS_DIR;
// Open list file
		$lh = @fopen(realpath($dr.'/'.$id.($tmp?'.'.$tmp:'').'.php'), 'r');
		if ($lh == FALSE)
			return gu_error('<br />'.t('Unable to read list file'));
// Read header from first line
		$header = explode("|", fgetss($lh));
		$list = new gu_list();
		$list->id = $header[0];
		$list->name = $header[1];
		$list->private = (bool)$header[2];
		$list->size = (int)$header[3];
		if ($load_addresses){	// Read all address lines
			$addresses = array();
			$update = false; //remove if old tmp address
			while (!feof($lh)){
				$address = trim(fgets($lh));
				if (strlen($address) > 0){
					if($tmp){//remove old temporary @dresses (cron)
						$a = explode(';',$address);
//cron by user
						if($a[0]+(gu_config::get('days')*86400) < time()){//86400 seconds = 1 day (24*60*60) :: remove temp > 15 days (default) = 1296000s 
							$update = true; 
							continue;
						}
					}
					$addresses[] = $address;
				}
			}
			$list->addresses = $addresses;
			if($update)
				$list->update('i');//remove if old tmp address
		}
		fclose($lh);
		gu_debug('gu_list::get(id: '.$id.', load_addresses: '.($load_addresses ? 'TRUE' : 'FALSE').', istmp: '.($tmp?'Y':'N').') time : '.number_format((int)(microtime() - $time_start), 7).' secs');
		return $list;
	}
	/**
	 * Creates a new address list
	 * @param string $name The list name
	 * @param bool $private TRUE if the list should be private (default is FALSE)
	 * @param array $addresses
	 * @return mixed The new list if it was successfully created, else FALSE
	 */
	public static function create($name, $private = FALSE, $addresses = NULL){
		if ($name == '' || preg_match('[^a-zA-Z0-9 \-]', $name))
			return gu_error('<br />'.t("Invalid list name. Names must only contain alphanumeric characters, spaces and dashes"));
// Demo mode check for number of addresses
		if (isset($addresses) && gu_is_demo() && count($addresses) >= GUTUMA_DEMO_MAX_LIST_SIZE)
			return gu_error('<br />'.t('Lists can have a maximum of % addresses in demo mode',array(GUTUMA_DEMO_MAX_LIST_SIZE)));
// Check for duplicate name
		$all_lists = gu_list::get_all();
		foreach ($all_lists as $l){
			if (strcasecmp($l->name, $name) == 0)
				return gu_error('<br />'.t('A list with the name <b><i>%</i></b> already exists',array($name)));
		}
// Demo mode check for number of lists
		if (gu_is_demo() && count($all_lists) >= GUTUMA_DEMO_MAX_NUM_LISTS)
			return gu_error('<br />'.t("You can have a maximum of % lists in demo mode",array(GUTUMA_DEMO_MAX_NUM_LISTS)));
		$list = new gu_list();
		$list->id = time();
		$list->name = $name;
		$list->private = $private;
		$list->addresses = isset($addresses) ? $addresses : array();
		if (!$list->update())// Save the list
			return FALSE;
		$listi = clone($list);//csv fix #TEP
		$listi->addresses = array();
		if (!$listi->update('i'))// Save the new temporary empty list
			return gu_error('<br />'.t('Error when create temporary list.'));
		return $list;
	}
	/**
	 * Imports an address list from a CSV file
	 * @param string $name The list name
	 * @param string $path The path of the CSV file
	 * @return mixed The new list if it was successfully created, else FALSE
	 */
	public static function import_csv($name, $path, $sep = ',', $first = 0){
		$csv = @fopen($path, 'r');
		if ($csv == FALSE)
			return gu_error('<br />'.t("Unable to open CSV file for reading"));
		$addresses = array();
		while (!feof($csv)){// Read addresses from first cell on each line
			$vals = explode($sep, fgets($csv));
			$address = trim($vals[0]);
			if (strlen($address) > 0 && strlen($address) <= GUTUMA_MAX_ADDRESS_LEN)
				$addresses[] = $address;
		}
		fclose($csv);
		if (!empty($first) && isset($addresses[0]))
			unset($addresses[0]);
		$addresses = array_unique($addresses);
		natcasesort($addresses);// Sort addresses alphabetically	
		return gu_list::create($name, FALSE, $addresses);
	}
	/**
	 * Gets the list with the specified name
	 * @param string $name The list name
	 * @param bool $load_addresses TRUE is list addresses should be loaded (default FALSE)
	 * @param string $tmp : empty | i | o : list ²Opt IN&OUT²
	 * @return mixed The list or FALSE if no such list exists
	 */
	public static function get_by_name($name, $load_addresses = FALSE, $tmp = ''){
		$lists = gu_list::get_all(FALSE, TRUE, $tmp);
		foreach ($lists as $l){
			if ($l->name == $name)
				return $load_addresses ? gu_list::get($l->id, TRUE, $tmp) : $l;
		}
		return FALSE;
	}
	/**
	 * Loads all of the lists
	 * @param bool $load_addresses TRUE if lists addresses should be loaded (default is FALSE)
	 * @param bool $inc_private TRUE if private lists should included (default is TRUE)
	 * @param string $tmp : empty | i : list ²Opt IN&OUT²
	 * @return mixed Array of lists or FALSE if an error occured
	 */
	public static function get_all($load_addresses = FALSE, $inc_private = TRUE, $tmp = ''){
		$dr = $tmp?GUTUMA_TEMP_DIR:GUTUMA_LISTS_DIR;
		$lists = array();
		if ($dh = @opendir(realpath($dr))){
			while (($file = readdir($dh)) !== FALSE){
				if ($file[0] != "." && !is_dir($file) && str_ends($file, '.php')){
					$list = gu_list::get(substr($file, 0, strlen($file - 4)), $load_addresses, $tmp);
					if (!isset($list->name))
						$list->update('i');
					if ($inc_private || !$list->private)
						$lists[$list->name] = $list;
				}
			}
			closedir($dh);
		}
		ksort($lists);
		return $lists;
	}
}
