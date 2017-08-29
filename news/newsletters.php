<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The outbox page
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
if (is_get_var('send')){
	$newsletter = gu_newsletter::get((int)get_get_var('send'));
	if ($newsletter !== FALSE){	
		$mailer = new gu_mailer();
		if ($mailer->init())
			$newsletter->send_batch($mailer);
	}
}
$box = is_get_var('box') ? get_get_var('box') : 'drafts';
$mailbox = gu_newsletter::get_mailbox();
$newsletters = $mailbox[$box];
gu_theme_start();
//gu_theme_messages();
?>
<script type="text/javascript">
/* <![CDATA[ */
	function gu_newsletter_draft_menu(id){
		return '<a href="compose.php?msg=' + id + '" class="imglink" title="<?php echo t('Edit and send');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_mail.png" /></a>&nbsp;&nbsp;'  
		      +'<a href="javascript:gu_newsletter_delete(' + id + ')" class="imglink" title="<?php echo t('Delete');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete.png" /></a>';
	}
	function gu_newsletter_outbox_menu(id){
		return '<a href="newsletters.php?box=outbox&send=' + id + '" class="imglink" title="<?php echo t('Send to remaining recipients');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send.png" /></a>&nbsp;&nbsp;'
		      +'<a href="javascript:gu_newsletter_delete(' + id + ')" class="imglink" title="<?php echo t('Delete');?>"><img src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete.png" /></a>';
	}
	function gu_newsletter_delete(id){
		if (confirm("<?php echo t('Are you sure you want to delete this newsletter?');?>")){
			gu_messages_clear();
			var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");
			mysack.execute = 1;
			mysack.method = "POST";
			mysack.setVar("action", "newsletter_delete");
			mysack.setVar("newsletter", id);
			mysack.onError = function(){ gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
			mysack.onCompletion = function(){ gu_messages_display(1000); }
			mysack.runAJAX();
		}
	}
	function gu_ajax_on_newsletter_delete(id){
		gu_element_set_background("row_" + id, '#FFDDDD');
		gu_element_fade_out("row_" + id, 1000);
		var count = parseInt(document.newsletters_form.num_newsletters.value) - 1;
		document.newsletters_form.num_newsletters.value = count;
		if (count == 0)
			setTimeout('gu_element_set_display("row_empty", "table-row")', 1000);
		gu_element_set_inner_html("mailbox_<?php echo $box; ?>_count", count);
	}
/* ]]> */
</script>
<?php
include_once 'themes/'.gu_config::get('theme_name').'/_newsletters.php';//Body
gu_theme_end();