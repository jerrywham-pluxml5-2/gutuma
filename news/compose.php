<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The compose page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
include_once 'inc/gutuma.php';
include_once 'inc/newsletter.php';
include_once 'inc/mailer.php';
gu_init();
gu_theme_start();
// Get the modified flag of the current newsletter
$is_modified = is_post_var('is_modified') ? get_post_var('is_modified') : FALSE;
// Get all available lists
$lists = gu_list::get_all();
if (is_get_var('msg'))
	// Load newsletter from draft if one was specified
	$newsletter = gu_newsletter::get((int)get_get_var('msg'));
else{// Create empty newsletter, and fill from post vars if they exist
	$newsletter = new gu_newsletter();
	if (is_post_var('msg_id')) 			$newsletter->set_id((int)get_post_var('msg_id'));
	if (is_post_var('msg_recips')) 	$newsletter->set_recipients(get_post_var('msg_recips'));
	if (is_post_var('msg_subject'))	$newsletter->set_subject(get_post_var('msg_subject'));
	if (is_post_var('msg_html')) 		$newsletter->set_html(get_post_var('msg_html'));
	if (is_post_var('msg_text'))
		$newsletter->set_text(get_post_var('msg_text'));
	else
		$newsletter->generate_text();
}
if ($newsletter->get_recipients() == '' && is_get_var('list')){// Take recipient list from querystring if none specified thus far
	foreach ($lists as $list){
		$list_id = (int)get_get_var('list');
		if ($list->get_id() == $list_id){
			$newsletter->set_recipients($list->get_name());
			break;
		}
	}
}
$preview_mode = is_post_var('preview_submit');// Preview the newsletter
if (is_post_var('send_submit')){// Send the newsletter
	if ($newsletter->send_prepare()){// Saves newsletter to outbox
		$mailer = new gu_mailer();
		if ($mailer->init()){
			if ($newsletter->send_batch($mailer)){
				if ($newsletter->is_sending())
					gu_success(t('Newsletter sent to first batch of recipients'));
				else
					gu_success(t('Newsletter sent to all recipients'));
			}
		}
		$newsletter = new gu_newsletter();
		$is_modified = FALSE;
	}
}
elseif (is_post_var('attach_submit') && $_FILES['attach_file']['name'] != ''){// Add an attachment
	if ($newsletter->store_attachment($_FILES['attach_file']['tmp_name'], $_FILES['attach_file']['name']))
		gu_success(t('Attachment <b><i>%</i></b> added',array($_FILES['attach_file']['name'])));
}
elseif (is_post_var('remove_submit')){// Remove an attachment
	$attachment = get_post_var('msg_attachments');
	if ($newsletter->delete_attachment($attachment))
		gu_success(t('Attachment <i>%</i> removed',array($attachment)));
}
elseif (is_post_var('save_submit')){
	if ($newsletter->save()){
		$is_modified = FALSE;
		gu_success(t('Newsletter saved as draft'));
	}
}
$mailbox = gu_newsletter::get_mailbox();// Get all newsletters as mailbox
$attachments = $newsletter->get_attachments();// Get list of attachments
if (!$preview_mode){
?>
<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	tinyMCE.init({// General options
		mode : 'textareas',
		skin: 'lightgray',
		theme : 'modern',//
		language : '<?php echo $_SESSION['lang'] ?>',
		relative_urls : false,
		remove_script_host : false,
		plugins : 'anchor autolink charmap code codesample colorpicker contextmenu emoticons fullpage fullscreen help hr image imagetools insertdatetime link lists media nonbreaking noneditable pagebreak paste print searchreplace spellchecker tabfocus table template textcolor textpattern toc visualblocks visualchars wordcount',//ok
/*
		// Theme options advanced (old tiny.2x) v4 of advanced theme exist ?
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste,pastetext,pasteword,|,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,hr,|,sub,sup,|,link,unlink,anchor,image,charmap,emotions,iespell,media,|,forecolor,backcolor,|,undo,redo,cleanup,code,print,help",
		theme_advanced_buttons3 : null,
		theme_advanced_buttons4 : null,
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		theme_advanced_resizing_max_width : 680,
*/
		// Drop lists for link/image/media
		//external_link_list_url : "lists/link_list.js",
		//external_image_list_url : "lists/image_list.js",
		//media_external_list_url : "lists/media_list.js",

		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		//content_css : "themes/default/editor.css",

		setup : function(ed){ ed.on('change',function(e){ gu_set_modified(true); }); }
	});
</script>
<?php } ?>
<script type="text/javascript">
/* <![CDATA[ */
	var is_post_back = false;
	function gu_presend_check(){
		if (document.send_form.msg_subject.value == "")
			return confirm("<?php echo t('Are you sure you want to send a message with an empty subject?');?>");
		return true;
	}
	function gu_add_recipient(){
		var txtRecips = document.send_form.msg_recips;
		var lstRecips = document.send_form.send_lists;
		txtRecips.value = gu_trim(txtRecips.value);		
		if (txtRecips.value != '')
			txtRecips.value += "; ";
		txtRecips.value += lstRecips.value;
		gu_set_modified(true);
	}
	window.onbeforeunload = function (ev){
		var is_modified = document.getElementById('is_modified').value;
		
		if (!is_post_back && is_modified)
			return "<?php echo t('Your message has not been sent or saved, and will be lost if you leave this page.');?>";
	}	
	function gu_cancel_unsaved_warning(){
		is_post_back = true;
	}
	function gu_set_modified(modified){
		document.getElementById('is_modified').value = (modified ? 1 : 0);
	}
/* ]]> */
</script>
<?php
include_once 'themes/'.gu_config::get('theme_name').'/_compose.php';//Body
gu_theme_end();