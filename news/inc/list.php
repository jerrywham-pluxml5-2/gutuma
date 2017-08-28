<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The address list class and functions
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 *  @version 1.4
 * @date	23/09/2013
 * @author	Cyril MAGUIRE
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
	/**
	 * Checks to see if this list contains the given address
	 * @param string $address The address to look for
	 * @return bool TRUE if the list contains the address
	 */
	public function contains($address){
		return in_array($address, $this->addresses);
	}
	/**
	 * Adds the specified address to this list
	 * @param string $address The address to add
	 * @param bool $update TRUE if list should be updated, else FALSE
	 * @return bool TRUE if the address was successfully added
	 */
	public function add($address, $update){
		if ($this->contains($address))
			return gu_error(t('Address <b><i>%</i></b> already in the list',array($address)));
		if (strlen($address) > GUTUMA_MAX_ADDRESS_LEN)
			return gu_error(t('Addresses cannot be more than % characters',array(GUTUMA_MAX_ADDRESS_LEN)));
		if (gu_is_demo() && count($this->addresses) >= GUTUMA_DEMO_MAX_LIST_SIZE)
			return gu_error(t('Lists can have a maximum of % addresses in demo mode',array(GUTUMA_DEMO_MAX_LIST_SIZE)));
// Add and then sort addresses alphabetically
		$this->addresses[] = $address;
		natcasesort($this->addresses);
		if ($update){
			if (!$this->update())
				return FALSE;
		}
		return TRUE;
	}
	/**
	 * Removes the specified address from this list
	 * @param string $address The address to remove
	 * @param bool $update TRUE if list should be updated, else FALSE	 
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function remove($address, $update){
// Create new address array minus the one being removed
		$found = false;
		$newaddresses = array();
		foreach ($this->addresses as $a){
			if ($address != $a)
				$newaddresses[] = $a;
			else
				$found = true;
		}
		if (!$found)
			return gu_error(t('Address <b><i>%</i></b> not found in the list <b><i>%</i></b>',array($address,$this->name)));
		$this->addresses = $newaddresses;
		if ($update){
			if (!$this->update())
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
	public function select_addresses($filter, $start, $count, &$filtered_total){
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
		return array_slice($addresses, ($start), $count);
	}
	/**
	 * Updates this address list, i.e., saves any changes
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function update(){
		$lh = @fopen(realpath(GUTUMA_LISTS_DIR).'/'.$this->id.'.php', 'w');
		if ($lh == FALSE)
			return gu_error(t('Unable to write list file. Check permissions for directory <code>%</code>',array(GUTUMA_LISTS_DIR)));
		fwrite($lh, "<?php die(); ?>".$this->id.'|'.$this->name.'|'.($this->private ? '1' : '0').'|'.count($this->addresses)."\n");
		foreach ($this->addresses as $a)
			fwrite($lh, $a."\n");
		fclose($lh);
		return TRUE;
	}
	/**
	 * Deletes this address list
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function delete(){
		if (!@unlink(realpath(GUTUMA_LISTS_DIR.'/'.$this->id.'.php')))
			return gu_error(t('Unable to delete list. Check permissions for directory <code>%</code>',array(GUTUMA_LISTS_DIR)));
		return TRUE;
	}
	/**
	 * Gets the list with the specified id
	 * @param int $id The list id
	 * @param bool $load_addresses TRUE is list addresses should be loaded (default FALSE)
	 * @return mixed The list or FALSE if an error occured
	 */
	public static function get($id, $load_addresses = FALSE){
		$time_start = microtime();
// Open list file
		$lh = @fopen(realpath(GUTUMA_LISTS_DIR.'/'.$id.'.php'), 'r');
		if ($lh == FALSE)
			return gu_error(t('Unable to read list file'));
// Read header from first line
		$header = explode("|", fgetss($lh));
		$list = new gu_list();
		$list->id = $header[0];
		$list->name = $header[1];
		$list->private = (bool)$header[2];
		$list->size = (int)$header[3];
		if ($load_addresses){	// Read all address lines
			$addresses = array();
			while (!feof($lh)){
				$address = trim(fgets($lh));
				if (strlen($address) > 0)
					$addresses[] = $address;
			}
			$list->addresses = $addresses;
		}
		fclose($lh);
		gu_debug('gu_list::get('.$id.', '.($load_addresses ? 'TRUE' : 'FALSE').') '.(microtime() - $time_start).' secs');
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
			return gu_error(t("Invalid list name. Names must only contain alphanumeric characters, spaces and dashes"));
// Demo mode check for number of addresses
		if (isset($addresses) && gu_is_demo() && count($addresses) >= GUTUMA_DEMO_MAX_LIST_SIZE)
			return gu_error(t('Lists can have a maximum of % addresses in demo mode',array(GUTUMA_DEMO_MAX_LIST_SIZE)));
// Check for duplicate name
		$all_lists = gu_list::get_all();
		foreach ($all_lists as $l){
			if (strcasecmp($l->name, $name) == 0)
				return gu_error(t('A list with the name <b><i>%</i></b> already exists',array($name)));
		}
// Demo mode check for number of lists
		if (gu_is_demo() && count($all_lists) >= GUTUMA_DEMO_MAX_NUM_LISTS)
			return gu_error(t("You can have a maximum of % lists in demo mode",array(GUTUMA_DEMO_MAX_NUM_LISTS)));
		$list = new gu_list();
		$list->id = time();
		$list->name = $name;
		$list->private = $private;
		$list->addresses = isset($addresses) ? $addresses : array();
		if (!$list->update())// Save the list
			return FALSE;
		return $list;
	}
	/**
	 * Imports an address list from a CSV file
	 * @param string $name The list name
	 * @param string $path The path of the CSV file
	 * @return mixed The new list if it was successfully created, else FALSE
	 */
	public static function import_csv($name, $path, $sep = ','){
		$csv = @fopen($path, 'r');
		if ($csv == FALSE)
			return gu_error(t("Unable to open CSV file for reading"));
		$addresses = array();
		while (!feof($csv)){// Read addresses from first cell on each line
			$vals = explode($sep, fgets($csv));
			$address = trim($vals[0]);
			if (strlen($address) > 0 && strlen($address) <= GUTUMA_MAX_ADDRESS_LEN)
				$addresses[] = $address;
		}
		fclose($csv);
		$addresses = array_unique($addresses);
		natcasesort($addresses);// Sort addresses alphabetically	
		return gu_list::create($name, FALSE, $addresses);
	}
	/**
	 * Gets the list with the specified name
	 * @param string $name The list name
	 * @param bool $load_addresses TRUE is list addresses should be loaded (default FALSE)
	 * @return mixed The list or FALSE if no such list exists
	 */
	public static function get_by_name($name, $load_addresses = FALSE){
		$lists = gu_list::get_all();
		foreach ($lists as $l){
			if ($l->name == $name)
				return $load_addresses ? gu_list::get($l->id, TRUE) : $l;
		}
		return FALSE;
	}
	/**
	 * Loads all of the lists
	 * @param bool $load_addresses TRUE if lists addresses should be loaded (default is FALSE)
	 * @param bool $inc_private TRUE if private lists should included (default is TRUE)
	 * @return mixed Array of lists or FALSE if an error occured
	 */
	public static function get_all($load_addresses = FALSE, $inc_private = TRUE){
		$lists = array();
		if ($dh = @opendir(realpath(GUTUMA_LISTS_DIR))){
			while (($file = readdir($dh)) !== FALSE){
				if (!is_dir($file) && str_ends($file, '.php')){
					$list = gu_list::get(substr($file, 0, strlen($file - 4)), $load_addresses);
					if ($inc_private || !$list->private)
						$lists[] = $list;
				}
			}
			closedir($dh);
		}
		return $lists;
	}
}