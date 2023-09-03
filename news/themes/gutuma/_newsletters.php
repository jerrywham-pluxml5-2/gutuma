<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included outbox page
 * @modifications Cyril Maguire, Thomas Ingles
 *
 * Gutama plugin package
 * @version 2.2.2           1.6
 * @date    08/08/2023      09/06/2017
 * @author  Thomas Ingles,  Cyril MAGUIRE
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
<p id="mvto" class="notification success" style="opacity:0"></p>
<form method="post" name="newsletters_form" id="newsletters_form" action=""><input name="num_newsletters" type="hidden" id="num_newsletters" value="<?php echo count($newsletters); ?>" />
	<table class="results table full-width" id="table1" border="0" cellspacing="0" cellpadding="0">
		<caption id="title_plus"><?php echo t('Newsletters');?> (<?php echo t(ucfirst($box)); ?>)</caption>
		<thead>
			<tr>
				<td><?php echo t('Subject');?></td>
				<td><?php echo t('Recipients');?></td>
				<td><?php echo t($box == 'drafts'?'Status':'Progress');?></td>
				<td><?php echo t('Modified on');?></td>
				<td><?php echo t('Created on');?></td>
				<td data-sortable="false" title="<?php echo t('Actions');?>" class="action" style="text-align: right"><script type="text/javascript">document.write(gu_newsletter_thead_menu())</script></td>
			</tr>
		</thead>
		<tbody>
<?php
if ($counter) {
	foreach($newsletters as $newsletter) {
		$id = $newsletter->get_id();
		$st = $newsletter->get_sended_time();
		$ok = $newsletter->is_writable()?'':'" title="'.strip_tags(ERROR_EXTRA).'/'.$id.'" class="error nowritable';
?>
			<tr id="row_<?php echo $id.$ok; ?>">
				<td class="word-wrap" data-content="<?php echo $newsletter->get_subject();#v2.2.2 ?>"><a href="javascript:gu_newsletter_msg('<?php echo $id; ?>');" title="<?php echo t('Preview'); ?>"><?php echo str_limit($newsletter->get_subject(), 40); ?></a></td>
				<td class="word-wrap" data-content="<?php echo $newsletter->get_recipients();#v2.2.2 ?>"><?php echo str_limit($newsletter->get_recipients(), 40); ?></td>
<?php if ($box == 'drafts') { ?>
					<td data-content="<?php echo $st;?>"><?php echo $st?t('Sended'):t('Draft') ?></td>
<?php } elseif ($box == 'outbox') { ?>
					<td data-content="<?php $stats = $newsletter->get_send_progress(); echo ($stats[1] - $stats[0]);?>"><?php echo (($stats[1] - $stats[0]).'/'.$stats[1]); ?></td>
<?php } ?>

				<td class="mini" data-content="<?php echo $newsletter->get_msg_time();#v2.2.2 ?>"><?php echo $newsletter->get_msg_date();#v2.2.1 ?></td>
				<td class="mini" data-content="<?php echo $newsletter->get_created_time();#v2.2.2 ?>"><?php echo $newsletter->get_created_date();#v2.2.1 ?></td>
				<td class="action" style="text-align: right" data-content="<?php echo $id;#$newsletter->get_created_time();#v2.2.2 ?>">
<?php if ($newsletter->is_unlocked()) { ?>
<?php  if ($box == 'drafts') { ?>
<a href="compose.php?msg=<?php echo $id; ?>" class="imglink noscript" title="<?php echo t('Edit and send');?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_mail.png" /></a><span class="gu-hide noscript">&nbsp;&nbsp;</span>
<?php  } # drafts ?>
<a href="newsletters.php?box=outbox&send=<?php echo $id .'&'. ($box!='drafts'?'draft':'out') . '=1'; ?>" class="imglink noscript" title="<?php echo t('Go to '. ($box!='drafts'?'drafts':'outbox')); ?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_draft.png" /></a>
<script type="text/javascript">document.write(gu_newsletter_menu(<?php echo $id; ?>,'<?php echo $box ?>'))</script>
<?php }else{?>
<img width="16px" class="imglink" src="themes/<?php echo gu_config::get('theme_name');?>/images/icon_error.png" />
<?php } ?></td>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>
	<div id="row_empty" style="display:<?php echo (count($newsletters) > 0)?'none':'block'; ?>">
		<p class="emptyresults"><span><?php echo t('No newsletters');?></span></p>
	</div>
</form>
<p>&nbsp;</p>
