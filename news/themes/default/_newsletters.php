<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included outbox page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

?>

<div id="sectionheader">
	<h2><?php echo t('Newsletter');?> <?php echo $box; ?></h2>

	<ul id="sectionmenu">
		<li><a href="compose.php"><?php echo t('Compose');?></a></li>
		<li><a href="newsletters.php?box=drafts" <?php echo ($box == 'drafts') ? 'class="current"' : '' ?>><?php echo t('Drafts');?> (<span id="mailbox_drafts_count"><?php echo count($mailbox['drafts']) ?></span>)</a></li>
		<li><a href="newsletters.php?box=outbox" <?php echo ($box == 'outbox') ? 'class="current"' : '' ?>><?php echo t('Outbox');?> (<span id="mailbox_outbox_count"><?php echo count($mailbox['outbox']) ?></span>)</a></li>
	</ul>

</div>
<?php gu_theme_messages(); ?>

<?php if ($box == 'drafts') { ?>
<p><?php echo t('These are the newsletters which have not yet been sent.');?> </p>
<?php } elseif ($box == 'outbox') { ?>
<p><?php echo t('These are the newsletters which have been sent but have not yet been delivered to all recipients.');?> </p>
<?php } ?>
<form method="post" name="newsletters_form" id="newsletters_form" action=""><input name="num_newsletters" type="hidden" id="num_newsletters" value="<?php echo count($newsletters); ?>" />
	<table border="0" cellspacing="0" cellpadding="0" class="results">
    <tr>
      <td><strong><?php echo t('Subject');?></strong></td>
      <td><strong><?php echo t('Recipients');?></strong></td>
<?php if ($box == 'drafts') { ?>
			<td>&nbsp;</td>  
<?php } elseif ($box == 'outbox') { ?>
      <td><strong><?php echo t('Progress');?></strong></td>
<?php } ?> 
			<td>&nbsp;</td> 
    </tr>
<?php
if (count($newsletters) > 0) {
	foreach($newsletters as $newsletter) {
?>
		<tr id="row_<?php echo $newsletter->get_id(); ?>">
      <td><?php echo str_limit($newsletter->get_subject(), 40); ?></td> 	  
      <td><?php echo str_limit($newsletter->get_recipients(), 40); ?></td>
<?php if ($box == 'drafts') { ?>
			<td>&nbsp;</td>    
			<td style="text-align: right"><script type="text/javascript">document.write(gu_newsletter_draft_menu(<?php echo $newsletter->get_id(); ?>))</script></td>
<?php } elseif ($box == 'outbox') { ?>
			<td><?php $stats = $newsletter->get_send_progress(); echo (($stats[1] - $stats[0]).'/'.$stats[1]); ?></td>
			<td style="text-align: right"><script type="text/javascript">document.write(gu_newsletter_outbox_menu(<?php echo $newsletter->get_id(); ?>))</script></td>
<?php } ?>
    </tr>
<?php
	}
}
?>
		<tr id="row_empty" style="display: <?php echo (count($newsletters) == 0) ? 'table-row' : 'none'; ?>"><td colspan="4" class="emptyresults"><?php echo t('No newsletters');?></td></tr>
	</table> 
</form>
<p>&nbsp;</p>