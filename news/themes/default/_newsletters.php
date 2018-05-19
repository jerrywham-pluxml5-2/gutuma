<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included outbox page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
?>
<div id="sectionheader" class="inline-form action-bar">
	<h2><?php echo t('Newsletters');?> (<?php echo t(ucfirst($box)); ?>)</h2>
	<p id="sectionmenu" class="plx<?php echo str_replace('.','',PLX_VERSION) ?>">
		<a href="compose.php" class="button"><?php echo t('Compose');?></a></li>
		<a href="newsletters.php?box=drafts" class="button<?php echo ($box == 'drafts') ? ' blue' : '' ?>"><?php echo t('Drafts');?> (<span id="mailbox_drafts_count"><?php echo count($mailbox['drafts']) ?></span>)</a>
		<a href="newsletters.php?box=outbox" class="button<?php echo ($box == 'outbox') ? ' blue' : '' ?>"><?php echo t('Outbox');?> (<span id="mailbox_outbox_count"><?php echo count($mailbox['outbox']) ?></span>)</a>
	</p>
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
			<td>&nbsp;</td>
<?php if ($box == 'drafts') { ?>
			<td>&nbsp;</td>
<?php } elseif ($box == 'outbox') { ?>
			<td><strong><?php echo t('Progress');?></strong></td>
<?php } ?>
			<td><strong><?php echo t('Subject');?></strong></td>
			<td><strong><?php echo t('Recipients');?></strong></td>
		</tr>
<?php
if (count($newsletters) > 0) {
	foreach($newsletters as $newsletter) {
?>
		<tr id="row_<?php echo $newsletter->get_id(); ?>">
<?php if ($box == 'drafts') { ?>
			<td><script type="text/javascript">document.write(gu_newsletter_draft_menu(<?php echo $newsletter->get_id(); ?>))</script></td>
			<td>&nbsp;</td>
<?php } elseif ($box == 'outbox') { ?>
			<td><script type="text/javascript">document.write(gu_newsletter_outbox_menu(<?php echo $newsletter->get_id(); ?>))</script></td>
			<td><?php $stats = $newsletter->get_send_progress(); echo (($stats[1] - $stats[0]).'/'.$stats[1]); ?></td>
<?php } ?>
			<td><?php echo str_limit($newsletter->get_subject(), 40); ?></td> 	  
			<td><?php echo str_limit($newsletter->get_recipients(), 40); ?></td>
		</tr>
<?php
	}
}
?>
		<tr id="row_empty" style="display: <?php echo (count($newsletters) == 0) ? 'table-row' : 'none'; ?>"><td colspan="4" class="emptyresults"><?php echo t('No newsletters');?></td></tr>
	</table>
</form>