<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The subscribe page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/


include_once 'inc/gutuma.php';
include_once 'inc/subscription.php';

// Initialize Gutuma without validation or redirection
gu_init(FALSE, FALSE);

gu_theme_start('ok');

// If variables have been posted then they may have prefixes
$posted_address = '';
$posted_lists = array();
$posted_action = '';
foreach (array_keys($_POST) as $var) {
	$val = get_post_var($var);
	
	if (strpos($var, 'subscribe_address') !== FALSE)
		$posted_address = trim($val);
	elseif (strpos($var, 'subscribe_list') !== FALSE)
		$posted_lists = is_array($val) ? $val : (((int)$val != 0) ? array((int)$val) : array()); // May be an array or an int
	elseif (strpos($var, 'unsubscribe_submit') !== FALSE)
		$posted_action = 'unsubscribe';
	elseif (strpos($var, 'subscribe_submit') !== FALSE)
		$posted_action = 'subscribe';
}

// Check if we've got what we need to do a posted (un)subscription
if ($posted_action != '' && $posted_address != '' && (count($posted_lists) > 0)) {
	gu_subscription_process($posted_address, $posted_lists, ($posted_action == 'subscribe'));
}

// Get querystring variables
$list_id = is_get_var('list') ? (int)get_get_var('list') : 0;
$address = is_get_var('addr') ? get_get_var('addr') : '';
$action = is_get_var('action') ? get_get_var('action') : '';

// Check if we've got what we need to do a querystring (un)subscription
if ($list_id > 0 && $address != '' && $action != '') {
	$list_ids = array($list_id);
	gu_subscription_process($address, $list_ids, ($subscribe == 'subscribe'));
}

// Check to see if a single valid list was specified
if ($list_id > 0 || count($posted_lists) == 1) {
	if ($list_id == 0)
		$list_id = $posted_lists[0];
	$list = gu_list::get($list_id , FALSE);
}
else
	// Load all non-private lists
	$lists = gu_list::get_all(FALSE, FALSE);

if ($address == '' && $posted_address != '')
	$address = $posted_address;

?>

<script type="text/javascript">
/* <![CDATA[ */
	function checkSubmit(form)
	{
		if (form.subscribe_address.value == "" || !gu_check_email(form.subscribe_address.value)) {
			alert("<?php echo t('You must enter a valid email address');?>");
			return false;
		}
		return true;
	}
/* ]]> */
</script>

<?php
//Body
include_once 'themes/'.gu_config::get('theme_name').'/_subscribe.php';


gu_theme_end();
?>
