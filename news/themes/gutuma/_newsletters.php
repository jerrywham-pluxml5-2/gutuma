<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included outbox page
 * @modifications Cyril Maguire, Thomas I.
 */
/* Gutama plugin package
 * @version 1.6
 * @date	09/06/2017
 * @author	Cyril MAGUIRE
*/

include_once '_menu.php';?>

<div id="sectionheader">
	<div style="float: left;"><h2><?php echo t('Newsletter');?>  <?php echo t(ucfirst($box)); ?></h2></div>
	<div style="float: right;">
		<ul id="sectionmenu">
			<li><a href="compose.php"><?php echo t('Compose');?></a></li>
			<li><a href="newsletters.php?box=drafts" <?php echo ($box == 'drafts') ? 'class="current"' : '' ?>><?php echo t('Drafts');?> (<span id="mailbox_drafts_count"><?php echo count($mailbox['drafts']) ?></span>)</a></li>
			<li><a href="newsletters.php?box=outbox" <?php echo ($box == 'outbox') ? 'class="current"' : '' ?>><?php echo t('Outbox');?> (<span id="mailbox_outbox_count"><?php echo count($mailbox['outbox']) ?></span>)</a></li>
		</ul>
	</div>
</div>
<?php gu_theme_messages(); ?>
<?php if ($box == 'drafts') { ?>
<p><?php echo t('These are the newsletters that can be modified before they are sent.');?> </p>
<?php } elseif ($box == 'outbox') { ?>
<p><?php echo t('These are the newsletters which have been sent but have not yet been delivered to all recipients.');?> </p>
<div id="autobatch" style="display:<?php echo($auto_send)? '': 'none'?>"><span id="countdown"></span><span id="autobatchmenu"></span><progress value="0" max="<?php echo $batch_time_limit ?>" id="progressBar"></progress></div>
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
			<td><strong><?php echo t('Modified on');?></strong></td>
			<td><strong><?php echo t('Created on');?></strong></td>
			<td title="<?php echo t('Actions');?>" class="action" style="text-align: right"><input id="allin" type="checkbox" onclick="checkAll(this.form, 'idNew[]')" /><script type="text/javascript">document.write(gu_newsletter_thead_menu())</script></td>
		</tr>
<?php
if (count($newsletters) > 0) {
	foreach($newsletters as $newsletter) {
?>
		<tr id="row_<?php echo $newsletter->get_id(); ?>">
			<td class="word-wrap"><?php echo str_limit($newsletter->get_subject(), 40); ?></td>
			<td class="word-wrap"><?php echo str_limit($newsletter->get_recipients(), 40); ?></td>
<?php if ($box == 'drafts') { ?>
			<td>&nbsp;</td>
<?php } elseif ($box == 'outbox') { ?>
			<td><?php $stats = $newsletter->get_send_progress(); echo (($stats[1] - $stats[0]).'/'.$stats[1]); ?></td>
<?php } ?>
			<td class="mini"><?php echo $newsletter->get_msg_date();#v2.2.1 ?></td>
			<td class="mini"><?php echo $newsletter->get_created_date();#v2.2.1 ?></td>
			<td class="action" style="text-align: right"><script type="text/javascript">document.write(gu_newsletter_menu(<?php echo $newsletter->get_id(); ?>,'<?php echo $box ?>'))</script></td>
		</tr>
<?php
	}
}
?>
		<tr id="row_empty" style="display: <?php echo (count($newsletters) == 0) ? 'table-row' : 'none'; ?>"><td colspan="6" class="emptyresults"><?php echo t('No newsletters');?></td></tr>
	</table>
</form>
<p>&nbsp;</p>
