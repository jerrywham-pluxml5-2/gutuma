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
$send_id = '0';
$auto_send = $auto_check = false;
$batch_max_size = gu_config::get('batch_max_size');
$batch_time_limit = intval(gu_config::get('batch_time_limit'));
$posted = FALSE;

if (is_get_var('send')){
	$send_id = intval(get_get_var('send'));
	$newsletter = gu_newsletter::get($send_id);

	#ReDraft since 2.2.1
	if (is_get_var('draft')){
		$newsletter->send_to_draft();
		$type = 'success';
		$msg = '#' .t('Drafts') . ' #' . $send_id;#gu_error|success()
		if(isset($_SERVER['GU_ERROR_MSG'])){
			$type = 'error';
			$msg .=' ' . $_SERVER['GU_ERROR_MSG'];#gu_error()
		}
		$_SESSION['GU_SEND_BATCH'] = array($type, $msg);#gu_error|success()
		$mailbox = gu_newsletter::get_mailbox();
		$box = empty($mailbox['outbox'])?'drafts':'outbox';
		header('Location:newsletters.php?box='.$box);exit;#Restore to drafts finished
	}

	#$posted = '#error';
	if ($newsletter !== FALSE){
		$mailer = new gu_mailer();
		if ($mailer->init()){
			$sended = $newsletter->get_send_progress();
			$newsletter->send_batch($mailer);
			$sendreal = $sended[0] > $batch_max_size? $batch_max_size: $sended[0]; # $sended[1] . ' - ' . $sended[0];
			# Reload page every batch_time_limit parameter (cron by browser)
			if (is_get_var('auto')){#AutoBatch since 2.2.1
				$outboxcount = is_get_var('count')? get_get_var('count'): 0;
				if(!isset($_SERVER['GU_ERROR_MSG']) AND $sended[0] > $batch_max_size){#AutoBatch : ALL IN 1 shot @fixé : aucun retour d'info $_SERVER['GU_ERROR_MSG']
					$_SESSION['GU_SEND_BATCH'] = array('success', '#AutoBatch  : ' . t('Outbox') . ' #'. $send_id . ' (' . $sendreal . ' / ' . $sended[0] . ' ' . t('emails') . ')');#AutoBatch success
					#gu_success();#$_SESSION['GU_SUCCESS_MSG'] = $_SERVER['GU_SUCCESS_MSG'];
					$auto_send = true;#animate
					$outboxcheck = '';
					#AutoBatchCheckeds
					if(is_get_var('check')){
						$auto_check = true;
						$boxcheck = get_get_var('check');
						$outboxcheck = '&check=' . $boxcheck;
						$send_ids = explode('O', $boxcheck);
						$send_ids[] = $send_id;#Add 2 checkeds in list
						$_SESSION['GU_SEND_BATCH'][1] .= PHP_EOL . '<br />' . PHP_EOL .'#AutoBatchCheckeds';
					}
					#newsletters.php?box=outbox&send=###########&auto=###&count=#[&check=[##########O##########]]
					header('refresh:'.$batch_time_limit.';url=newsletters.php?box=outbox&send='.$send_id.'&auto='.$batch_time_limit.'&count='.$outboxcount.$outboxcheck);# auto refresh batch_time_limit
				}elseif(isset($_SERVER['GU_ERROR_MSG'])){
					$_SESSION['GU_SEND_BATCH'] = array('error', '#AutoBatch ' . t('Outbox') . ' #'. $send_id . ' (' . $sendreal . ' / ' . $sended[0] . ' ' . t('emails') . ') ' . $_SERVER['GU_ERROR_MSG']);#gu_error()
					header('Location:newsletters.php?box=outbox');exit;#Have unfinished
				}else{
					$_SESSION['GU_SEND_BATCH'] = array('success', '<b style="color:darkgreen">#AutoBatch √</b> : ' . t('Outbox') . ' #'. $send_id . ' (' . $sended[1] . ' ' . t('emails') . ')');#gu_success()
					if($outboxcount < 2){
						header('Location:newsletters.php?box=drafts');exit;#ALL IN 1 shot is finished && empty task : return to drafts
					}else{
						if(is_get_var('check')){
							$auto_send = true;#animate
							$auto_check = true;

							$boxcheck = get_get_var('check');
							$outboxcheck = '&check=' . $boxcheck;
							$send_ids = explode('O', $boxcheck);

							if($sendreal <= $batch_max_size){
								$send_id = $send_ids[0];#shift2Next
								unset($send_ids[0]);#remove
								$outboxcheck = '&check=' . (empty($send_ids)? '': implode('O', $send_ids));
								$send_ids[] = $send_id;#Restore 2 checked on last list
								$newsletter = gu_newsletter::get($send_id);
								if ($newsletter !== FALSE){
									$sended = $newsletter->get_send_progress();
									$outboxcount--;
								}else{
									$_SESSION['GU_SEND_BATCH'] = array('error', '#AutoBatch #Checkeds #next : ' .t('Outbox') . ' #'. $send_id . ' ERROR (UNKNOWN LIST ID)');#AutoBatch error
									header('Location:newsletters.php?box=outbox');exit;#STOP ALL finished
								}
								$_SESSION['GU_SEND_BATCH'][1] .= PHP_EOL . '<br />' . PHP_EOL .'#AutoBatch #Checkeds #next : ' .t('Outbox') . ' #' . $send_id . ' OK (' . $sended[0] . ' ' . t('emails') . ')';
							}
							#newsletters.php?box=outbox&send=###########&auto=###&count=#[&check=[##########O##########]]
							header('refresh:'.$batch_time_limit.';url=newsletters.php?box=outbox&send='.$send_id.'&auto='.$batch_time_limit.'&count='.$outboxcount.$outboxcheck);# auto refresh batch_time_limit
						}#get check
						else{
							#AutoBatchCheckeds
							header('Location:newsletters.php?box=outbox');exit;#ALL IN 1 shot is finished
						}
					}
				}
			}#fi get var auto #batch
			else{
				//~ $_SESSION['GU_SEND_BATCH'] = array('error', '#AutoBatch LIST : '. $send_id . ' NO (' . $sendreal . ' / ' . $sended[0] . ' ' . t('emails') . ')');#AutoBatch success
				$type = 'success';
				$msg = '#Batch : ' .t('Outbox') . ' #' . $send_id . ' (' . $sendreal . ' / ' . $sended[0] . ' ' . t('emails') . ')';#gu_error|success()
				if(isset($_SERVER['GU_ERROR_MSG'])){
					$type = 'error';
					$msg .=' ' . $_SERVER['GU_ERROR_MSG'];#gu_error()
				}
				$_SESSION['GU_SEND_BATCH'] = array($type, $msg);#gu_error|success()

				header('Location:newsletters.php?box=outbox');exit;#ALL IN 1 shot is bad finished
			}
		}#mailer init
	}#newsletter
}#send
#notifications
if(isset($_SESSION['GU_SEND_BATCH'])){
	$funk_name = 'gu_' . $_SESSION['GU_SEND_BATCH'][0];
	$funk_name($_SESSION['GU_SEND_BATCH'][1]);# Call gu_{success|error}($msg);
	unset($_SESSION['GU_SEND_BATCH']);
}
#evite le repost
if($posted){
	var_dump($posted, $_SERVER['REQUEST_URI']);exit;#'Location: ' . $_SERVER['REQUEST_URI'] + EXIT;
	$_SESSION['gu_posted'] = $posted;#gu_success
	gu_redirect($_SERVER['REQUEST_URI']);#'Location: ' . $_SERVER['REQUEST_URI'] + EXIT;
}
$box = is_get_var('box') ? get_get_var('box') : 'drafts';
$mailbox = gu_newsletter::get_mailbox();
$newsletters = $mailbox[$box];
gu_theme_start();
//gu_theme_messages();
?>
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
	function gu_newsletter_menu(id, type){
		var auto_class = id == <?php echo $auto_send?$send_id:'0' ?>? 'animate': '';
		return '<input type="checkbox" name="idNew[]" value="' + id + '"/>&nbsp;&nbsp;'
			  +((type=='outbox')//!draft
			  ?'<a href="newsletters.php?box=outbox&send=' + id + '&auto=<?php echo $batch_time_limit ?>&count=<?php echo count($newsletters) ?>" class="imglink" title="<?php echo t('Send to remaining recipients') ?> #AutoBatch : <?php echo $batch_max_size . t('emails') . ' / ' . $batch_time_limit . ' ' . t('seconds') ?>"><img width="16px" class="' + auto_class + '" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send_auto.png" /></a>'
			  +'&nbsp;<span class="gu-hide">&nbsp;</span>'
			  +'<a href="newsletters.php?box=outbox&send=' + id + '" class="imglink" title="<?php echo t('Send to remaining recipients');?> (<?php echo $batch_max_size . t('emails') ?>)"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send.png" /></a>'
			  +'&nbsp;<span class="gu-hide">&nbsp;</span>'
			  +'<a href="newsletters.php?box=outbox&send=' + id + '&draft=1" class="imglink" title="<?php echo t('Restore to drafts');?>" onclick="return confirm(\'<?php echo t('Restore to drafts');?>?\')"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_draft.png" /></a>'
			  :'<a href="compose.php?msg=' + id + '" class="imglink" title="<?php echo t('Edit and send');?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_mail.png" /></a>')
			  +'<span class="gu-hide">&nbsp;&nbsp;<img width="16px" class="imglink" src="themes/<?php echo gu_config::get('theme_name') ?>/images/1px.png" />&nbsp;</span>'
			  +'&nbsp;<a href="javascript:gu_newsletter_delete(' + id + ')" class="imglink" title="<?php echo t('Delete');?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete.png" /></a>';
	}
	//remove checkeds
	function gu_newsletter_thead_menu(){/* In table header. After "Action" */
		var auto_class = '<?php echo $auto_send&&$auto_check?'animate':'' ?>';
		return ''
			+(('<?=$box?>'=='outbox')//!draft
			  ?'&nbsp;&nbsp;<a href="javascript:gu_newsletters_autobatch_checked()" class="imglink" title="<?php echo t('Send to remaining recipients') ?> #AutoBatchChecked : <?php echo $batch_max_size . t('emails') . ' / ' . $batch_time_limit . ' ' . t('seconds') ?>"><img width="16px" class="' + auto_class + '" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send_auto.png" /></a>'
			  :'')
			  <?php
				$mg = ($box == 'drafts')? 2: 3;
				for($i=0;$i<$mg;$i++) echo "+'" . '<span'.(!$i?' class="gu-hide"':'').'>&nbsp;&nbsp;<img width="16px" class="imglink" width="16px" src="themes/'.gu_config::get('theme_name').'/images/1px.png"></span>'."'";
?>		+'&nbsp;&nbsp;'
			+'<a href="javascript:gu_newsletters_delete()" class="imglink" title="<?php echo t('Delete');?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete_red.png" /></a>';
	}
<?php if($auto_check AND !empty($send_ids)){ ?>
window.onload=function(){
	setTimeout(function(){
		//gu_newsletters_autobatch_checkeds : idNew[]
		var send_ids = '<?= implode('O',$send_ids) ?>';
		send_ids = send_ids.split('O');
		var lists = document.getElementsByName('idNew[]');
		for(var i = 0; i < lists.length; i++){
			var pos = send_ids.indexOf(lists[i].value);
			if( pos !== -1 ){
				lists[i].checked = true;
			}
		}
	},10);
}
<?php }#fi $auto_check ?>

	function gu_newsletters_autobatch_checked(){
		//idNew[]
		var lists = document.getElementsByName('idNew[]');
		var ok, ck = 0;
		var ids = [];
		for(var i = 0; i < lists.length; i++){
			if(lists[i].checked){
				ck = !0;
				if(!ok){
					ok = confirm("<?php echo t('Autobatch with selected newsletters') . ' (' . $batch_max_size . ' ' . t('emails') . ' / ' . $batch_time_limit . ' ' . t('seconds') ?>)?");//Are you sure you want sends selected newsletters with auto batch
					if(!ok) return;//only one time ;)
				}
				ids.push(lists[i].value);
			}
		}
//Check before anim
		if(!ck) {document.getElementById('allin').checked = true;setTimeout(function(){document.getElementById('allin').checked = false;},777);return;}
		if(!ok) return;
		var send_id = ids.shift();//ids[0] && remove 1st elem
		var batch_time_limit = <?php echo $batch_time_limit ?>;
		var outboxcount = parseInt(document.getElementById('mailbox_outbox_count').innerHTML);
		var outboxcheck = ids.join('O');

		window.location.href = 'newsletters.php?box=outbox&send=' + send_id + '&auto=' + batch_time_limit + '&count=' + outboxcount + '&check=' + outboxcheck;
	}


	delAllNew = false;/* global */
	function gu_newsletters_delete(){
		var lists = document.getElementsByName('idNew[]');
		var fadeTime = ok = 0;
		for(var i = 0; i < lists.length; i++){
			if(lists[i].checked){
				if(!ok){
					ok = confirm("<?php echo t('Are you sure you want to delete selected newsletters?');?>");
					if(!ok) return;//only one time ;)
				}
				var id = lists[i].value;
				setTimeout('gu_newsletter_delete("' + id + '", "222")', fadeTime);//gu_newsletter_delete(list_id)
				fadeTime = fadeTime + 444;
			}
		}
	}
//todo add event listener on check TO HIDE/SHOW btn deleteChecks to gu_lists_delete()

	function gu_newsletter_delete(id, fadeTime){
		var fadeTime = fadeTime?fadeTime:1000;
		var all = !(fadeTime == 1000);
		if (all || confirm("<?php echo t('Are you sure you want to delete this newsletter?');?>")){
			gu_messages_clear();
			delAllNew = all;/* global */
			var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");
			mysack.execute = 1;
			mysack.method = "POST";
			mysack.setVar("action", "newsletter_delete");
			mysack.setVar("newsletter", id);
			mysack.onError = function(){ gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
			mysack.onCompletion = function(){ gu_messages_display(fadeTime); }
			mysack.runAJAX();
		}
	}
	function gu_ajax_on_newsletter_delete(id){
		var fadeTime = delAllNew?222:1000;//fix for multiple
		gu_element_set_background("row_" + id, '#FFDDDD');
		gu_element_fade_out("row_" + id, 1000);
		var count = parseInt(document.newsletters_form.num_newsletters.value) - 1;
		document.newsletters_form.num_newsletters.value = count;
		if (count == 0)
			setTimeout('gu_element_set_display("row_empty", "table-row")', 1000);
		gu_element_set_inner_html("mailbox_<?php echo $box; ?>_count", count);
		delAllNew = false;/* global */
	}

<?php if($auto_send):#stackoverflow.com/questions/31106189/ddg#31106229 ?>
	var totaltime = timeleft = <?php echo $batch_time_limit ?>;
	var txt = clr ='Go!';
	var links = '&nbsp;&nbsp;<img width="16px" class="animate" title="#AutoBatch <?php echo $send_id ?>" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send_auto.png" />'
	+'&nbsp;&nbsp;<img width="16px" class="imglink" src="themes/<?php echo gu_config::get('theme_name') ?>/images/1px.png" />'
	+'&nbsp;&nbsp;<a href="newsletters.php?box=outbox" class="imglink" title="Stop #AutoBatch <?php echo $send_id ?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send_stop.png" /></a>'
	+'&nbsp;&nbsp;';
	var sendTimer = setInterval(function(){
		if(links){
			document.getElementById('autobatchmenu').innerHTML = links;
			links = false;
		}
		if(timeleft <= 0)
			txt = clr;
		else
			txt = timeleft + 's';
		document.getElementById('countdown').innerHTML = '#AutoBatch ' + txt;
		document.getElementById('progressBar').value = totaltime - timeleft;
		timeleft -= 1;
		if(txt == clr)
			clearInterval(sendTimer);
	}, 1000);
<?php endif;#$auto_send ?>

/* ]]> */
</script>
<?php
include_once 'themes/'.gu_config::get('theme_name').'/_newsletters.php';//Body
gu_theme_end();