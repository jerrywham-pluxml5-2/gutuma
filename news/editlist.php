<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The list editing page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 2.0.0
 * @date	23/09/2018
 * @author	Cyril MAGUIRE, Thomas Ingles
*/
include_once 'inc/gutuma.php';
gu_init();
// Get id of list to edit from querystring
$list_id = is_get_var('list') ? (int)get_get_var('list') : 0;
$start = is_get_var('start') ? (int)get_get_var('start') : 0;
$filter = is_get_var('filter') ? get_get_var('filter') : '';
$tmp = is_get_var('tmp') ? get_get_var('tmp') : '';
$list = gu_list::get($list_id, TRUE, $tmp);// Load real/tmp list data
$mist = gu_list::get($list_id, TRUE, ($tmp?'':'i'));// Load tmp/real list data (other face)
$maddresses = $mist->get_addresses();// other face for comparison
$seeOtherFace = TRUE;
if(!$tmp){//only if is real
	$listi = gu_list::get($list_id, TRUE, 'i');//Temp list
	$seeOtherFace = !!$listi->get_size();
	for($a=0;$a<count($maddresses);$a++){//remove all timestamp of tmp list
		$maddresses[$a] = explode(';',$maddresses[$a]);
		$maddresses[$a] = $maddresses[$a][1];
	}
}
$posted = FALSE;
if (is_post_var('list_update')){// Make updates
	$list->set_name(get_post_var('list_name'));
	$list->set_private(is_post_var('list_private'));
	$list->set_friend(get_post_var('list_friend'));#friendly name #since 2.2.1
	if (!$list->update())// Save the real list
		return gu_error('<br />'.t('Error when update % list.',array(t('real'))));
	if(!$tmp){//only if is real
		$listi->set_name(get_post_var('list_name'));
		$listi->set_private(is_post_var('list_private'));
		if (!$listi->update('i'))// Save the temporary list
			return gu_error('<br />'.t('Error when update % list.',array(t('temporary'))));
	}
	$posted = t('List updated').' ('.t('real').(!$tmp?' &amp; '.t('temporary'):'').')';#gu_success
}
elseif (is_post_var('new_address')){
	$address = trim(get_post_var('new_address'));
	if ($list->add($address, TRUE, '', 'ADMIN_EDITLIST_FAKE_KEY'))
		$posted = t('Address <b><i>%</i></b> added to list',array($address));
}
#evite le repost
if($posted){
	$_SESSION['gu_posted'] = $posted;#gu_success
	gu_redirect($_SERVER['REQUEST_URI']);#'Location: ' . $_SERVER['REQUEST_URI'] + EXIT;
}
gu_theme_start();
?>
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
	gu_status_message_delay = 1000;
	function filter_addresses(form){
		var filter = form.filter_list_name.value;
		window.location = "editlist.php?list=" + <?php echo $list_id.($tmp?' + "&tmp=i"':''); ?> + (filter != "" ? ("&filter=" + filter) : "");
	}
	function reset_filter(form){
		form.filter_list_name.value = "";
		filter_addresses(form);
	}
	function check_add(form){
		if (form.new_address.value == "" || !gu_check_email(form.new_address.value)) {
			alert("<?php echo t('You must enter a valid email address');?>");
			return false;
		}
		return true;
	}

	//remove checkeds
	function gu_editlist_thead_menu(){/* In table header. After "Action" */
		return '<input type="checkbox" onclick="checkAll(this.form, \'idMel[]\')"/>&nbsp;<a href="javascript:gu_remove_addresses()" class="imglink" title="<?php echo t('Delete');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete_red.png" /></a>';
	}

	delAllMel = false;/* global */
	function gu_remove_addresses(){
		var tmp = '';//Or 'i'
		var lists = document.getElementsByName('idMel[]');
		var fadeTime = ok = 0;
		for(var i = 0; i < lists.length; i++){
			if(lists[i].checked){
				if(!ok){
					ok = confirm("<?php echo t('Are you sure you want to remove checked addresses?');?>");
					if(!ok) return;//only one time ;)
				}
				var ml = lists[i].value;
				var id = lists[i].id.split('-')[1];
				lists[i].checked = false;//Fix on reclick : no removed in DOM ;)
				setTimeout('gu_remove_address("' + ml + '", "' + id + '", "' + tmp + '", "222")', fadeTime);//gu_remove_address(address, address_id, tmp, fadeTime)
				fadeTime = fadeTime + 444;
			}
		}
	}
//todo add event listener on check TO SHOW btn deleteChecks to gu_lists_delete()

	function gu_remove_address(address, address_id, tmp, fadeTime){
		var fadeTime = fadeTime?fadeTime:1000;
		var all = !(fadeTime == 1000);
		if (all || confirm("<?php echo t('Are you sure you want to remove this address?');?>")) {
			gu_messages_clear();
			delAllMel = all;/* global */
			var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");
			mysack.execute = 1;
			mysack.method = "POST";
			mysack.setVar("action", "remove_address");
			mysack.setVar("list", <?php echo $list_id; ?>);
			if(tmp) mysack.setVar("tmp", 'i');
			mysack.setVar("address", address);
			mysack.setVar("address_id", address_id);
			mysack.onError = function() { gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
			mysack.onCompletion = function() { gu_messages_display(fadeTime); }
			mysack.runAJAX();
		}
	}
	function gu_ajax_on_remove_address(address_id){//, msg :  semble inutilisé ici
		var fadeTime = delAllMel?222:1000;//fix for multiple
		gu_element_set_background("row_" + address_id, "#FFDDDD");
		gu_element_fade_out("row_" + address_id, 1000);
		var old_size = parseInt(document.edit_form.num_addresses.value);
		var new_size = old_size - 1;
		var old_size_filter = parseInt(document.getElementById("pager_addresses_total").innerHTML);
		var new_size_filter = old_size_filter - 1;
		var pg_start = parseInt(document.getElementById("pager_addresses_start").innerHTML);
		var old_pg_size = parseInt(document.getElementById("pager_addresses_end").innerHTML);
		var new_pg_size = old_pg_size - 1;
		document.edit_form.num_addresses.value = new_size;
		//~ document.getElementById("pager_addresses_end").innerHTML = new_size % <?php echo GUTUMA_PAGE_SIZE; ?>;
		document.getElementById("pager_addresses_end").innerHTML = new_pg_size;
		document.getElementById("pager_addresses_total").innerHTML = new_size_filter;
		document.edit_form.num_addresses.value = new_size;
		if (new_size == 0 || new_size_filter == 0) {
			setTimeout('gu_element_set_display("pager_addresses", "none")', fadeTime);
			setTimeout('gu_element_set_display("row_empty", "table-row")', fadeTime);
		}else
		if (new_pg_size < pg_start){
			if(document.getElementById("pager_addresses_prev")){
				setTimeout('window.location=document.getElementById("pager_addresses_prev").href', fadeTime);
				return false;
			}
			if(new_pg_size == 0 && new_size_filter > 0){
				setTimeout('window.location.reload()', 1000);
				return false;
			}
		}
		delAllMel = false;/* global */
	}
/* ]]> */
</script>
<?php
//Body
include_once 'themes/'.gu_config::get('theme_name').'/_editlist.php';
gu_theme_end();