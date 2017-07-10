<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The list editing page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/


include_once 'inc/gutuma.php';

gu_init();
gu_theme_start();

// Get id of list to edit from querystring
$list_id = is_get_var('list') ? (int)get_get_var('list') : 0;
$start = is_get_var('start') ? (int)get_get_var('start') : 0;
$filter = is_get_var('filter') ? get_get_var('filter') : '';

// Load list data
$list = gu_list::get($list_id, TRUE);

// Make updates
if (is_post_var('list_update')) {
	$list->set_name(get_post_var('list_name'));
	$list->set_private(is_post_var('list_private'));	
	if ($list->update())
		gu_success(t('List updated'));
}
elseif (is_post_var('new_address')) {
	$address = trim(get_post_var('new_address'));
	if ($list->add($address, TRUE))
		gu_success(t('Address <b><i>%</i></b> added to list',array($address)));
}
?>

<?php //gu_theme_messages(); ?>
<script type="text/javascript">
/* <![CDATA[ */
	gu_status_message_delay = 1000;
	
	function filter_addresses(form)
	{
		var filter = form.filter_list_name.value;
		
		window.location = "editlist.php?list=" + <?php echo $list->get_id(); ?> + (filter != "" ? ("&filter=" + filter) : "");	
	}
	
	function reset_filter(form)
	{
		form.filter_list_name.value = "";
		filter_addresses(form);
	}
	
	function check_add(form)
	{
		if (form.new_address.value == "" || !gu_check_email(form.new_address.value)) {
			alert("<?php echo t('You must enter a valid email address');?>");
			return false;
		}
		return true;
	}
	
	function gu_remove_address(address, address_id)
	{
		if (confirm("<?php echo t('Are you sure you want to remove this address?');?>")) {
			gu_messages_clear();
			
			var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");    
			mysack.execute = 1;
			mysack.method = "POST";
			mysack.setVar("action", "remove_address");
			mysack.setVar("list", <?php echo $list_id; ?>);			
			mysack.setVar("address", address);
			mysack.setVar("address_id", address_id);			
			mysack.onError = function() { gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
			mysack.onCompletion = function() { gu_messages_display(1000); }
			mysack.runAJAX();
		}
	}
	
	function gu_ajax_on_remove_address(address_id, msg)
	{
		gu_element_set_background("row_" + address_id, "#FFDDDD");
		gu_element_fade_out("row_" + address_id, 1000);
		
		var old_size = parseInt(document.edit_form.num_addresses.value);
		var new_size = old_size - 1;
		document.edit_form.num_addresses.value = new_size;
		document.getElementById("pager_addresses_end").innerHTML = new_size % <?php echo GUTUMA_PAGE_SIZE; ?>;
		document.getElementById("pager_addresses_total").innerHTML = new_size;
		
		if (new_size == 0) {
			setTimeout('gu_element_set_display("pager_addresses", "none")', 1000);
			setTimeout('gu_element_set_display("row_empty", "table-row")', 1000);
		}
	}
/* ]]> */
</script>

<?php
//Body
include_once 'themes/'.gu_config::get('theme_name').'/_editlist.php';

gu_theme_end();
?>