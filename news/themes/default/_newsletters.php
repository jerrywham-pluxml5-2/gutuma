<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included outbox page
 * @modifications Cyril Maguire, Thomas Ingles
 *
 * Gutama plugin package
 * @version 2.2.1           1.6
 * @date    11/03/2020      01/10/2013
 * @author  Thomas Ingles,  Cyril MAGUIRE
*/
?>
<div id="sectionheader" class="inline-form action-bar">
	<h2><?php echo t('Newsletters');?> (<?php echo t(ucfirst($box)); ?>)</h2>
	<p id="sectionmenu" class="plx<?php echo str_replace('.','',PLX_VERSION) ?>">
		<a href="compose.php" class="h6 button"><?php echo t('Compose');?></a></li>
		<a href="newsletters.php?box=drafts" class="h6 button<?php echo ($box == 'drafts') ? ' blue' : '' ?>"><?php echo t('Drafts');?> (<span id="mailbox_drafts_count"><?php echo count($mailbox['drafts']) ?></span>)</a>
		<a href="newsletters.php?box=outbox" class="h6 button<?php echo ($box == 'outbox') ? ' blue' : '' ?>"><?php echo t('Outbox');?> (<span id="mailbox_outbox_count"><?php echo count($mailbox['outbox']) ?></span>)</a>
	</p>
</div>
<?php gu_theme_messages(); ?>
<?php if ($box == 'drafts') { ?>
<p><?php echo t('These are the newsletters that can be modified before they are sent.');?> </p>
<?php } elseif ($box == 'outbox') { ?>
<p><?php echo t('These are the newsletters which have been sent but have not yet been delivered to all recipients.');?> </p>
<div id="autobatch" style="display:<?php echo($auto_send)? '': 'none'?>"><span id="countdown"></span><span id="autobatchmenu"></span><progress value="0" max="<?php echo $batch_time_limit ?>" id="progressBar"></progress></div>
<?php } ?>
<form method="post" name="newsletters_form" id="newsletters_form" action=""><input name="num_newsletters" type="hidden" id="num_newsletters" value="<?php echo count($newsletters); ?>" />
	<div class="scrollable-table">
		<table border="0" cellspacing="0" cellpadding="0" class="results">
			<tr>
				<td class="checkbox<?php echo ($box == 'outbox'? ' checkbox-lrg': '');?>" title="<?php echo t('Actions');?>"><input id="allin" type="checkbox" onclick="checkAll(this.form, 'idNew[]')"/><script type="text/javascript">document.write(gu_newsletter_thead_menu())</script>&nbsp;&nbsp;&nbsp;</td>
<?php if ($box == 'drafts') { ?>
				<td>&nbsp;</td>
<?php } elseif ($box == 'outbox') { ?>
				<td><strong><?php echo t('Progress');?></strong></td>
<?php } ?>
				<td><strong><?php echo t('Last send');?></strong></td>
				<td><strong><?php echo t('Subject');?></strong></td>
				<td><strong><?php echo t('Recipients');?></strong></td>
				<td><strong><?php echo t('Modified on');?></strong></td>
				<td><strong><?php echo t('Created on');?></strong></td>
			</tr>
<?php
if (count($newsletters) > 0) {
	foreach($newsletters as $newsletter) {
?>
			<tr id="row_<?php echo $newsletter->get_id(); ?>">
			<td><script type="text/javascript">document.write(gu_newsletter_menu(<?php echo $newsletter->get_id(); ?>,'<?php echo $box ?>'))</script></td>

<?php if ($box == 'drafts') { ?>
				<td>&nbsp;</td>
<?php } elseif ($box == 'outbox') { ?>
				<td><?php $stats = $newsletter->get_send_progress(); echo (($stats[1] - $stats[0]).'/'.$stats[1]); ?></td>
<?php } ?>

				<td class="mini"><?php echo $newsletter->get_sended_date();#v2.2.1 ?></td>
				<td><?php echo str_limit($newsletter->get_subject(), 40); ?></td>
				<td><?php echo str_limit($newsletter->get_recipients(), 40); ?></td>
				<td class="mini"><?php echo $newsletter->get_msg_date();#v2.2.1 ?></td>
				<td class="mini"><?php echo $newsletter->get_created_date();#v2.2.1 ?></td>
			</tr>
<?php
	}
}
?>
			<tr id="row_empty" style="display: <?php echo (count($newsletters) == 0) ? 'table-row' : 'none'; ?>"><td colspan="7" class="emptyresults"><?php echo t('No newsletters');?></td></tr>
		</table>
	</div>
</form>