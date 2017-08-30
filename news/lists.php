<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The lists page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
include 'inc/gutuma.php';
gu_init();
gu_theme_start();
if (isset($_FILES['import_file'])){// Import CSV into new list if one has been uploaded
	$name = remove_ext(basename($_FILES['import_file']['name']));
	if ($_FILES['import_file']['type'] == 'text/csv'){
		$csv = $_FILES['import_file']['tmp_name'];
		if ($list = gu_list::import_csv($name, $csv, @$_POST['sep'], @$_POST['first'])){
			if ($list->update())
				gu_success(t('List <b><i>%</i></b> imported from CSV file',array($name)));
		}
		// Delete file
		if (is_file($csv)){
		unlink($csv);
		}
	} else{
		gu_error(t('Uploaded file is not a csv file'));
	}
}
$lists = gu_list::get_all();
?>
<?php //gu_theme_messages(); ?>
<script type="text/javascript">
/* <![CDATA[ */
	function gu_list_menu(list_id){
		return '<a href="editlist.php?list=' + list_id + '" class="imglink" title="<?php echo t('Edit');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_edit.png" /></a>&nbsp;&nbsp;'
		      +'<a href="compose.php?list=' + list_id + '" class="imglink" title="<?php echo t('Send newsletter to');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_mail.png" /></a>&nbsp;&nbsp;'
		      +'<a href="gencsv.php?list=' + list_id + '" class="imglink" title="<?php echo t('Download as CSV');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_download.png" /></a>&nbsp;&nbsp;'		  
		      +'<a href="javascript:gu_list_delete(' + list_id + ')" class="imglink" title="<?php echo t('Delete');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete.png" /></a>';
	}
	function gu_list_add(name, is_private){
		gu_messages_clear();
		document.add_form.new_list_name.value = "";
		document.add_form.new_list_private.checked = false;
		var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");
		mysack.execute = 1;
		mysack.method = "POST";
		mysack.setVar("action", "list_add");
		mysack.setVar("name", name);
		mysack.setVar("private", is_private ? 1 : 0);
		mysack.onError = function(){ gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
		mysack.onCompletion = function(){ gu_messages_display(1000); }
		mysack.runAJAX();
	}
	function gu_ajax_on_list_add(list_id, name, is_private){
		var count = parseInt(document.lists_form.num_lists.value) + 1;// Update list count
		document.lists_form.num_lists.value = count;// Update list counter
		gu_element_set_display("row_empty", "none");// Hide empty row
		var tbody = document.getElementById("liststable").tBodies[0];
		var row = document.createElement("tr");
		row.setAttribute("id", "row_" + list_id);
		row.setAttribute("style", "display: table-row; opacity: 0;");
		var cell1 = document.createElement("td");
		cell1.innerHTML = gu_list_menu(list_id);;
		var cell2 = document.createElement("td");
		cell2.innerHTML = is_private ? "<?php echo t('Yes');?>" : "<?php echo t('No');?>";
		var cell3 = document.createElement("td");
		cell3.innerHTML = '0';
		var cell4 = document.createElement("td");
		cell4.innerHTML = name;
		if('default' == '<?php echo gu_config::get('theme_name');?>'){
			cell3.innerHTML = '<b>(0)</b><span class="should-cut-off">'+cell4.innerHTML+'</span>';
			cell2.setAttribute("class", "sml-text-center");
			cell3.setAttribute("class", "cell-off");
//			row.appendChild(cell1+cell2+cell3);// TypeError: Argument 1 of Node.appendChild is not an object. :/
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
		}else{//Gutuma original order 
			cell1.setAttribute("style", "text-align: right");
			row.appendChild(cell4);
			row.appendChild(cell3);
			row.appendChild(cell2);
			row.appendChild(cell1);
		}
		tbody.appendChild(row);
		gu_element_fade("row_" + list_id, 1000, 0, 100);///gu_element_fade_in("row_" + list_id, 1618, "table-row");//dispay & hide (when message hide) fix
	}
	function gu_list_delete(list_id){
		if (confirm("<?php echo t('Are you sure you want to delete this list? All addresses will be lost!');?>")){
			gu_messages_clear();
			var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");
			mysack.execute = 1;
			mysack.method = "POST";
			mysack.setVar("action", "list_delete");
			mysack.setVar("list", list_id);
			mysack.onError = function(){ gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
			mysack.onCompletion = function(){ gu_messages_display(1000); }
			mysack.runAJAX();
		}
	}
	function gu_ajax_on_list_delete(list_id, msg){
		gu_element_set_background("row_" + list_id, '#FFDDDD');
		gu_element_fade_out("row_" + list_id, 1000);
		var count = parseInt(document.lists_form.num_lists.value) - 1;
		document.lists_form.num_lists.value = count;
		if (count == 0)
			setTimeout('gu_element_set_display("row_empty", "table-row")', 1000);
	}
/* ]]> */
</script>
<?php
//Body
include_once 'themes/'.gu_config::get('theme_name').'/_lists.php';
gu_theme_end();