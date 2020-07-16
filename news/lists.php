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
$posted = FALSE;
if (isset($_FILES['import_file'])){// Import CSV into new list if one has been uploaded
	$name = remove_ext(basename($_FILES['import_file']['name']));
	if ($_FILES['import_file']['type'] == 'text/csv'){
		$csv = $_FILES['import_file']['tmp_name'];
		if ($list = gu_list::import_csv($name, $csv, @$_POST['sep'], @$_POST['first'])){
			if ($list->update())
				$posted = t('List <b><i>%</i></b> imported from CSV file',array($name));
		}
		// Delete file
		if (is_file($csv)){
		unlink($csv);
		}
	} else {
		gu_error('<br />'.t('Uploaded file is not a csv file'));
	}
}
$lists = gu_list::get_all();//get_all($load_addresses = FALSE, $inc_private = TRUE, $tmp = '')
$listsTmp = gu_list::get_all(FALSE,TRUE,'i');//²IO
$listsTmpSize = array();
foreach($listsTmp as $listi)
	$listsTmpSize[$listi->get_id()] = $listi->get_size();

#evite le repost
if($posted){
	$_SESSION['gu_posted'] = $posted;#gu_success
	gu_redirect($_SERVER['REQUEST_URI']);#'Location: ' . $_SERVER['REQUEST_URI'] + EXIT;
}
gu_theme_start();
//gu_theme_messages();
?>
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
	function gu_list_menu(list_id, tmp){
		if(!!tmp)
			return '<a href="editlist.php?list=' + list_id + '&amp;tmp=i" class="imglink" title="<?php echo t('Edit');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_edit.png" /></a>&nbsp;&nbsp;';
		return '<input type="checkbox" name="idList[]" value="' + list_id + '">&nbsp;&nbsp;'
		      +'<a href="editlist.php?list=' + list_id + '" class="imglink" title="<?php echo t('Edit');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_edit.png" /></a>&nbsp;&nbsp;'
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
		mysack.setVar("k", "ADMIN");
		mysack.setVar("name", name);
		mysack.setVar("private", is_private ? 1 : 0);
		mysack.onError = function(){ gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); }
		mysack.onCompletion = function(){ gu_messages_display(1000); }
		mysack.runAJAX();
	}
	function gu_ajax_on_list_add(list_id, name, is_private){
		if(name == 'adherents') {//adhesion hack plugin to hide notif of missing list (here it's ajax)
			//adhesion hack plugin to hide alert notif
			var css = document.createElement("style");
			css.type = "text/css";
			css.innerHTML = ".warning { display:none; }";
			document.body.appendChild(css);
		}
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
		cell3.innerHTML = '<span id="size_' + list_id + 'i">0</span>';
		var cell4 = document.createElement("td");
		cell4.innerHTML = '<span id="size_' + list_id + '">0</span>';
		var cell5 = document.createElement("td");
		cell5.innerHTML = '<b><span class="should-cut-off">' + name + '</span></b>&nbsp;(<span class="should-cut-off">' + name + ')</span>';
		if('default' == '<?php echo gu_config::get('theme_name');?>'){
			cell3.innerHTML = '<b>(<span id="size_' + list_id + '">0</span>)</b><b>(<span id="size_' + list_id + 'i">0</span>)</b>'+cell5.innerHTML+'';
			cell2.setAttribute("class", "sml-text-center");
			cell3.setAttribute("class", "cell-off");
//			row.appendChild(cell1+cell2+cell3);// TypeError: Argument 1 of Node.appendChild is not an object. :/
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
		}else{//Gutuma original order
			cell1.setAttribute("style", "text-align: right");
			row.appendChild(cell5);
			row.appendChild(cell4);
			row.appendChild(cell3);
			row.appendChild(cell2);
			row.appendChild(cell1);
		}
		tbody.appendChild(row);
		gu_element_fade("row_" + list_id, 1000, 0, 100);//gu_element_fade_in("row_" + list_id, 1618, "table-row");//show & hide (when message hide) fix
	}

<?php
	$img = '';
	for($i=0;$i<3;$i++) $img .= '&nbsp;&nbsp;<img width="16px" class="imglink" width="16px" src="themes/'.gu_config::get('theme_name').'/images/1px.png">';
?>

	function gu_lists_tools_menu(){//Idea to simplify themes #tep: add to all (checked) lists
		return '<input type="button" onclick="gu_make_in_all_lists()" value="<?php echo t('Clear on all');?>" title="<?php echo t('Delete address on all list');?>." />'+
		'&nbsp;<input type="button" onclick="gu_make_in_all_lists(false,true)" value="<?php echo t('Clear on checked');?>" title="<?php echo t('Delete address on all checked list');?>." />'+
		' <input type="button" onclick="gu_make_in_all_lists(false,false,true)" value="<?php echo t('Add on all');?>" title="<?php echo t('Add address on all list');?>." />'+
		'&nbsp;<input type="button" onclick="gu_make_in_all_lists(false,true,true)" value="<?php echo t('Add on checked');?>" title="<?php echo t('Add address on all checked list');?>." />';
	}

	//remove checkeds
	function gu_lists_thead_menu(){/* In table header. After "Action" */
		return '<input type="checkbox" onclick="checkAll(this.form, \'idList[]\')"/><?php echo $img;?>&nbsp;&nbsp;<a href="javascript:gu_lists_delete()" class="imglink" title="<?php echo t('Delete');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete_red.png" /></a>';
	}

	delAllList = false;/* global */
	function gu_lists_delete(){
		var lists = document.getElementsByName('idList[]');
		var fadeTime = ok = 0;
		for(var i = 0; i < lists.length; i++){
			if(lists[i].checked){
				if(!ok){
					ok = confirm("<?php echo t('Are you sure you want to delete checked lists? All addresses will be lost!');?>");
					if(!ok) return;//only one time ;)
				}
				var id = lists[i].value;
				setTimeout('gu_list_delete("' + id + '", "222")', fadeTime);//gu_list_delete(list_id)
				fadeTime = fadeTime + 444;
			}
		}
	}
//todo add event listener on check TO SHOW btn deleteChecks to gu_lists_delete()

	function gu_list_delete(list_id, fadeTime){
		var fadeTime = fadeTime?fadeTime:1000;
		var all = !(fadeTime == 1000);
		if (all || confirm("<?php echo t('Are you sure you want to delete this list? All addresses will be lost!');?>")){
			gu_messages_clear();
			delAllList = all;/* global */
			var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");
			mysack.execute = 1;
			mysack.method = "POST";
			mysack.setVar("action", "list_delete");
			mysack.setVar("list", list_id);
			mysack.onError = function(){ gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
			mysack.onCompletion = function(){ gu_messages_display(fadeTime); }
			mysack.runAJAX();
		}
	}
	function gu_ajax_on_list_delete(list_id){
		var fadeTime = delAllList?222:1000;//fix for multiple
		gu_element_set_background("row_" + list_id, '#FFDDDD');
		gu_element_fade_out("row_" + list_id, fadeTime);
		var count = parseInt(document.lists_form.num_lists.value) - 1;
		document.lists_form.num_lists.value = count;
		if (count == 0)
			setTimeout('gu_element_set_display("row_empty", "table-row")', fadeTime);
		delAllList = false;/* global */
	}
	//Add/Remove one email in All/Selected list button + (idea in editlist /w eml js lnk)
	function gu_make_in_all_lists(email,lists,add){
		email = email?email:'@';
		if(lists){//checked list mode
			lists = document.getElementsByName('idList[]');
			var ids = '';
			for(var i = 0; i < lists.length; i++){
				if(lists[i].checked){
					ids = ids + lists[i].value + '·';
				}
			}
			lists = ids.trim('·');
			if(!lists){//zero checked list
				gu_success("<?php echo t('Check one or more list before!');?>");
				gu_messages_display(0);
				return;
			}
		}
		var ok = true;
		while(true){//stackoverflow.com/a/23097913
			email = (ok&&email&&email!='@')?email:prompt("<?php echo t('Enter a valid email address to "+(add?"add":"delete")+" it on all"+(lists?" checked":"")+" lists')?>", email);
			ok = !(email===null);
			var eml = ok?email.trim():email;
			eml = (!mailIsValid(eml))?false:eml;
			if (eml && confirm("<?php echo t('Are you sure to "+(add?"add":"remove")+" the following address on all"+(lists?" checked":"")+" lists?')?>\n\n"+eml+"\n")){
				gu_messages_clear();
				var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");
				mysack.execute = 1;
				mysack.method = "POST";
				mysack.setVar("action", "add_del_address");//add or del on all / selected lists
				mysack.setVar("address", eml);
				if(lists) mysack.setVar("lists", lists);
				if(add) mysack.setVar("add", 1);
				mysack.onError = function(){ gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
				mysack.onCompletion = function(){ gu_messages_display(1000); }
				mysack.runAJAX();
				break;
			}
			if(!eml && ok && confirm(email + " <?php echo t('is invalid!')?> <?php echo t('Retry?')?>")){
				ok = false;
				continue;
			}
			break;
		}
	}
	//Update list counts
	function gu_ajax_on_make_in_all_lists(address, strlists, add){
		if(!strlists) return;
		var lists = strlists.split('·');
		for(var i=0; i<lists.length; i++){
			var list = lists[i].split(':')
			var counter = document.getElementById('size_' + list[1]);//Get list count
			var old_size = parseInt(counter.innerHTML);//Get old value
			var new_size = list[0];//real size of list at this time
			var link_tmp = document.getElementById('link_' + lists[i]);//Get list count
			if(new_size<1&&link_tmp) link_tmp.style.display='none';
			if(new_size>0&&link_tmp) link_tmp.style.display='';
			new_size = new_size<1?0:new_size;//fix if unreloaded page -1, -2
			counter.innerHTML = new_size;//Set with new value
			counter.style.color = (add!='0'?'#22F122':'#F12222');//Set (text) color style if new value #visual/user friendly?
			setTimeout(razcolor.bind(null, counter), 2222);//stackoverflow.com/a/1190656
		}
		return;
	}

	function razcolor(e){//reset counter color
		e.style.color = '';
	}
/* ]]> */
</script>
<?php
//Body
include_once 'themes/'.gu_config::get('theme_name').'/_lists.php';
gu_theme_end();