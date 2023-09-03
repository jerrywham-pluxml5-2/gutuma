<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The outbox page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 2.2.2
 * @date	12/08/2023
 * @author	Cyril MAGUIRE, Thomas Ingles
*/
include_once 'inc/gutuma.php';
include_once 'inc/newsletter.php';
include_once 'inc/mailer.php';
gu_init();
$posted = FALSE;
$send_id = '0';
$auto_send = $auto_check = false;
$batch_max_size = intval(gu_config::get('batch_max_size'));
$batch_time_limit = intval(gu_config::get('batch_time_limit'));
#Fix : Fatal error: Maximum execution time of 30 seconds exceeded
@set_time_limit(30+$batch_time_limit);

if (is_get_var('send')){
	#Init For noscript only : since 2.2.2
	$send_id = intval(get_get_var('send'));
	$newsletter = gu_newsletter::get($send_id);
	$box = 'drafts';
	$type = 'success'; # gu_TYPE()
	$redirOk = false;
	#For noscript only : since 2.2.2
	if (is_get_var('draft')){#ReDraft since 2.2.1
		$newsletter->send_to_draft();
		$redirOk = true;
		$msg = t('Drafts');
		$mailbox = gu_newsletter::get_mailbox();
		if(!empty($mailbox['outbox'])) $box ='outbox';#
	}
	if (is_get_var('out')){#ReSend since 2.2.2
		$newsletter->draft_to_send();
		$redirOk = true;
		$msg = t('Outbox');
	}
	if ($redirOk){
		$subject = trim($newsletter->get_subject());
		$subject = !empty($subject)? $subject: '#'.$send_id.' ' .t('Empty subject');
		$msg = t('Newsletter moved') . ' -&gt; '.$msg.' (' . $subject . ')';
		if(isset($_SERVER['GU_ERROR_MSG'])){
			$type = 'error';
			$msg .= ' ' . $_SERVER['GU_ERROR_MSG'];#gu_error()
		}
		$_SESSION['GU_SEND_BATCH'] = array($type, $msg);#gu_error|success()
		header('Location:newsletters.php?box='.$box);exit;# Go to drafts if out is empty
	}#End For noscript only

	#$posted = '#error';
	if ($newsletter !== FALSE && $batch_max_size){
		$mailer = new gu_mailer();
		if ($mailer->init()){
			$sended = $newsletter->get_send_progress();//$afaire = $sended[1] - $sended[0];
			$sendreal = $sended[0] > $batch_max_size? $batch_max_size: $sended[0]; # $sended[1] . ' - ' . $sended[0];
			$send_time_max = time() + $batch_time_limit;
			$newsletter->send_batch($mailer);#envoi
			$sended = $newsletter->get_send_progress();//$fait = $sended[1] - $sended[0];#BAF
			$total = $sendreal - $sended[0];
			# Reload page every batch_time_limit parameter (cron by browser)
			if (is_get_var('auto')){#AutoBatch since 2.2.1
				if (isset($_SERVER['GU_ERROR_MSG'])){
					$_SESSION['GU_SEND_BATCH'] = array('error', '#AutoBatch : ' . t('Last send') . ' #'. $send_id . ' (' . $total . ' / ' .  $sendreal . ' ' . t('emails') . ') ' . $_SERVER['GU_ERROR_MSG']);#gu_error()
					header('Location:newsletters.php?box=outbox');exit;#Have unfinished
				}
				$auto_send = true;#animate
				$outboxcount = is_get_var('count')? get_get_var('count'): 0;
				$outboxcheck = '';

				$_SESSION['GU_SEND_BATCH'] = array('success', '<b style="color:darkgreen">#AutoBatch √</b> : ' . t('Last send') . ' #' . $send_id . ' (' . $total . ' / ' .  $sendreal . ' ' . t('emails') . ')');#gu_success()

				if (is_get_var('check')){
					$auto_check = true;
					$boxcheck = get_get_var('check');
					$outboxcheck = '&check=' . $boxcheck;
					$send_ids = explode('O', $boxcheck);
					# For calculate rest size
					$loop_size = $batch_max_size - $total;

					//~ if($total < $batch_max_size){#AutoBatch
					if(empty($sended[0])){#AutoBatch
						# Remove news
						$send_ids = array_diff($send_ids, array($send_id));

						foreach($send_ids as $i => $news_id){
							$send_id = $news_id;#shift2Next

							if($loop_size < 1) break;#StopLoop #Fix send_batch() 1 email

							$newsletter = gu_newsletter::get($send_id);
							if ($newsletter === FALSE){
								$_SESSION['GU_SEND_BATCH'] = array('error', '#AutoBatch #Checkeds #next : ' .t('Last send') . ' #'. $send_id . ' ERROR (UNKNOWN LIST ID)');#AutoBatch error
								header('Location:newsletters.php?box=outbox');exit;#STOP ALL finished
							}

							$sended = $newsletter->get_send_progress();//$afaire = $sended[1] - $sended[0];
							$sendreal = $sended[0] > $loop_size? $loop_size: $sended[0]; # $sended[1] . ' - ' . $sended[0];

							//~ $DEBUG = '<br />000 ; TOTAL='.$total.' ::TOT='.@$tot.' ::REAL='.$sendreal.' ::S0='.$sended[0].' ::LS='.$loop_size.' ___';
							$DEBUG = '';

							# Set batch limits for next send_batch or stop
							$rest_time = intval($send_time_max - time());
							if($rest_time < 1){break;}
							gu_config::set('batch_time_limit', $rest_time);
							gu_config::set('batch_max_size', $loop_size);#max batch tmp set???


							#todo
							#trad && good notifs -$DEBUG
							#why bad (negative) count of $tot #Cfait #tep #fixé
							#why if finished refresh send=#id#&check=#id# #Cfait #tep #fixé


							$newsletter->send_batch($mailer);#envoi
							$sended = $newsletter->get_send_progress();//$fait = $sended[1] - $sended[0];#BAF
							$tot = /*$sendreal - */($sended[1] - $sended[0]);

							$loop_size -= $tot;# Rest size
							$outboxcount--;

							//~ $DEBUG .= '<br />111 ; TOTAL='.$total.' ::TOT='.$tot.' ::REAL='.$sendreal.' ::S0='.$sended[0].' ::LS='.$loop_size.' ___';


							$total += $tot;

							//~ $DEBUG .= '<br />222 ; TOTAL='.$total.' ::TOT='.$tot.' ::REAL='.$sendreal.' ::S0='.$sended[0].' ::LS='.$loop_size.' ___';

							//~ $_SESSION['GU_SEND_BATCH'][1] .= $DEBUG . PHP_EOL . '<br />' . PHP_EOL .'#AutoBatch #Checkeds #loop : ' .t('Outbox') . ' #' . $send_id . ' (' . $tot . ' / ' . $sended[1] . ' ' . t('emails') . ')';
							$_SESSION['GU_SEND_BATCH'][1] .= $DEBUG . '<br />#AutoBatch #Checkeds #loop : ' .t('Outbox') . ' #' . $send_id . ' (' . $tot . ' / ' . $sended[1] . ' ' . t('emails') . ')';

							if(empty($sended[0])){
								# Remove news
								$send_ids = array_diff($send_ids, array($send_id));
								//~ if($total >= $loop_size){
									//~ break;
								//~ }
								//~ $_SESSION['GU_SEND_BATCH'][1] .= PHP_EOL . '<br />' . PHP_EOL .'#AutoBatch #Checkeds #next : ' .t('Outbox') . ' #' . $send_id . ' OK (' . $sended[0] . ' ' . t('emails') . ')';
								#$_SESSION['GU_SEND_BATCH'][1] .= '<br />#AutoBatch #Checkeds #next : ' .t('Outbox') . ' #' . $send_id . ' OK (' . $sended[0] . ' / ' . $sended[1] . ' ' . t('emails') . ')';
							}
							else{
								break;
							}
						}#HCAEROF

						$outboxcheck = empty($send_ids)? '': '&check=' . implode('O', $send_ids);
					}#FI empty $sended[0] /*$total <= $batch_max_size*/

					#4 DEBUG
					//~ $_SESSION['GU_SEND_BATCH'][1] .= PHP_EOL . '<br />' . PHP_EOL .'#AutoBatch #END LOOP : ' .t('Outbox') . ' #' . $send_id . ' OK (IL RESTE ' . $sended[0] . ' ' . t('emails') . ' ET '.count($send_ids).' Lettre(s) )';
					//~ $_SESSION['GU_SEND_BATCH'][1] .= '<br />#AutoBatch #END LOOP : ' .t('Outbox') . ' #' . $send_id . ' OK (IL RESTE ' . $sended[0] . ' ' . t('emails') . ' ET '.count($send_ids).' Lettre(s) )';
					$_SESSION['GU_SEND_BATCH'][1] .= '<br />#AutoBatch '.$total.' courriels envoyés.<br />Il reste '.count($send_ids).' lettre(s).';

#s'il reste 1 lettre et 0 envois location outbox au lieux d'un refresh #baf
//http://localhost/sel66_net.dev/plugins/gutuma/news/newsletters.php?box=outbox&send=1685681311&auto=200&count=1&check=1685681311
#AutoBatch √ : Envois #1685681311 (88 / 88 courriels)
#AutoBatch #END LOOP : Envois #1685681311 OK (IL RESTE 0 courriels ET 0 Lettre(s) )

					#step finish
					if(!$sended[0] && !count($send_ids)){
						#AutoBatchCheckeds
						header('Location:newsletters.php?box=outbox');exit;#ALL IN 1 shot is finished
					}


					#newsletters.php?box=outbox&send=###########&auto=###&count=#[&check=[##########O##########]]
					header('refresh:'.$batch_time_limit.';url=newsletters.php?box=outbox&send='.$send_id.'&auto='.$batch_time_limit.'&count='.$outboxcount.$outboxcheck);# auto refresh batch_time_limit
				}#get check
				else{
					#4 DEBUG
					//~ $_SESSION['GU_SEND_BATCH'][1] .= PHP_EOL . '<br />' . PHP_EOL .'#AutoBatch #FINISHED : ' .t('Outbox') . ' #' . $send_id . ' OK (' . $sended[0] . ' ' . t('emails') . ')';
#AutoBatch √ : Envois #1685681311 (0 / 0 courriels)
#AutoBatch #FINISHED : Envois #1685681311 OK (0 courriels)

					#AutoBatchCheckeds
					header('Location:newsletters.php?box=outbox');exit;#ALL IN 1 shot is finished
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
if($posted){#unused?
	#var_dump($posted, $_SERVER['REQUEST_URI']);exit;#'Location: ' . $_SERVER['REQUEST_URI'] + EXIT;
	$_SESSION['gu_posted'] = $posted;#gu_success
	gu_redirect($_SERVER['REQUEST_URI']);#'Location: ' . $_SERVER['REQUEST_URI'] + EXIT;
}
# init
$box = is_get_var('box') ? get_get_var('box') : 'drafts';
$mailbox = gu_newsletter::get_mailbox();
$newsletters = $mailbox[$box];
$counter = count($newsletters);
$xob = $box!='drafts'?'drafts':'outbox';#js gu_ajax_on_newsletter_move() ...
define('GU_DATATABLE', TRUE);# Auth for Inc Head with gu_datatableIncHead() in header themes ;-)
gu_theme_start();
//gu_theme_messages();
?>
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
	var dataTable = null;
	function gu_newsletter_menu(id, type){
		var auto_class = id == <?php echo $auto_send?$send_id:'0' ?>? ' animate': '';
		return '<input type="checkbox" name="idNew[]" value="' + id + '"/>&nbsp;&nbsp;'
			  +((type=='outbox')//!draft
			  ?'<a href="javascript:gu_newsletter_move(' + id + ',1000,\'<?php echo t('Go to drafts');?>?\')" class="imglink" title="<?php echo t('Go to drafts');?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_draft.png" /></a>'
			  +'&nbsp;<span class="gu-hide">&nbsp;</span>'
			  +'<a href="newsletters.php?box=outbox&amp;send=' + id + '&amp;auto=<?php echo $batch_time_limit ?>&amp;count=<?php echo count($newsletters) ?>&amp;check=' + id + '" class="imglink" title="<?php echo t('Send to remaining recipients') ?> #AutoBatch : <?php echo $batch_max_size . ' ' . t('emails') . ' / ' . $batch_time_limit . ' ' . t('seconds') ?>"><img width="16px" class="icon_send_auto' + auto_class + '" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send_auto.png" /></a>'
			  +'&nbsp;<span class="gu-hide">&nbsp;</span>'
			  +'<a href="newsletters.php?box=outbox&amp;send=' + id + '" class="imglink" title="<?php echo t('Send to remaining recipients');?> (<?php echo $batch_max_size . t('emails') ?>)"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send.png" /></a>'
			  :'<a href="compose.php?msg=' + id + '" class="imglink" title="<?php echo t('Edit and send');?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_mail.png" /></a>'
			  +'&nbsp;<span class="gu-hide">&nbsp;</span>'
			  +'<a href="newsletters.php?box=outbox&amp;send=' + id + '&amp;out=1" class="imglink" title="<?php echo t('Go to outbox');?>" onclick="return confirm(\'<?php echo t('Go to outbox');?>?\')"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send.png" /></a>')
			  +'<span class="gu-hide">&nbsp;&nbsp;<img width="16px" class="imglink" src="themes/<?php echo gu_config::get('theme_name') ?>/images/1px.png" />&nbsp;</span>'
			  +'&nbsp;<a href="javascript:gu_newsletter_delete(' + id + ')" class="imglink" title="<?php echo t('Delete');?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete.png" /></a>';
	}
	//remove checkeds
	function gu_newsletter_thead_menu(){/* In table header. After "Action" */
		var auto_class = '<?php echo $auto_send&&$auto_check?' animate':'' ?>';
		return '<input id="allin" type="checkbox" onclick="checkAll(this.form, \'idNew[]\')"/>'
			+'&nbsp;&nbsp;<a href="javascript:gu_newsletter_moves()" class="imglink" title="<?php echo t('Move to') . ' ' . t(ucFirst($xob)) ?> #AutoMoveChecked"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_move_auto.png" /></a>'
			+(('<?=$box?>'=='outbox')//!draft
			  ?'&nbsp;&nbsp;<a href="javascript:gu_newsletters_move_autobatch_checked()" class="imglink" title="<?php echo t('Send to remaining recipients') ?> #AutoBatchChecked : <?php echo $batch_max_size . t('emails') . ' / ' . $batch_time_limit . ' ' . t('seconds') ?>"><img width="16px" class="icon_send_auto' + auto_class + '" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send_auto.png" /></a>'
			  :'&nbsp;&nbsp;<a href="javascript:gu_newsletters_move_autobatch_checked()" class="imglink" title="<?php echo t('Send to all recipients') ?> #AutoSendChecked"><img width="16px" class="icon_send_auto' + auto_class + '" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_send_auto.png" /></a>')//Draft2Send
			  +''
			  <?php
				$mg = ($box == 'drafts')? 1: 2;
				for($i=0;$i<$mg;$i++) echo "+'" . '<span'.(!$i?' class="gu-hide"':'').'>&nbsp;&nbsp;<img width="16px" class="imglink" width="16px" src="themes/'.gu_config::get('theme_name').'/images/1px.png"></span>'."'";
?>			+'&nbsp;&nbsp;'
			+'<a href="javascript:gu_newsletter_deletes()" class="imglink" title="<?php echo t('Delete');?>"><img width="16px" src="themes/<?php echo gu_config::get('theme_name'); ?>/images/icon_delete_red.png" /></a>';
	}
window.addEventListener('load', function(){
<?php if($auto_check AND !empty($send_ids)){ ?>
	setTimeout(function(){
		//gu_newsletters_auto(batch|send)_checkeds : idNew[]
		var send_ids = '<?= implode('O',$send_ids) ?>';
		send_ids = send_ids.split('O');
		var lists = document.getElementsByName('idNew[]');
		for(var i = 0; i < lists.length; i++){
			var pos = send_ids.indexOf(lists[i].value);
			if( pos !== -1 ){
				lists[i].checked = true;
			}
		}
		gu_load_DataTable(!0);// One Page : maybe bad idea to load it...
	},10);
	//~ gu_success('Salve en cours, patience...');//todo trad
	//~ gu_messages_display(0);
<?php }elseif($counter){# ! $auto_check ?>
	gu_load_DataTable();
<?php }#fi $auto_check ?>
});//fi add onLoad
var memo_perpage = gu_memo('gu_perpage');//get
	function gu_load_DataTable(all){//beta
		for(var dt=1; dt<2; dt++){
			dataTable = new DataTable('#table'+dt, { //vanilla datatable
			//~ dataTable = new simpleDatatables.DataTable('#table'+dt, {//fiduswriter.github.io/Simple-DataTables-classic/7-init-destroy-import-export/
				searchable: true,
				fixedHeight: false,//false by default
				fixedColumns: false,//true by default
				perPageSelect: [5, 10, 15, 20, 25, 50, 100, 250, 1000, 100000],
				perPage:  !all?parseInt(memo_perpage?gu_memo('gu_perpage'):10):100000,
				// Customise the display text
				labels: {
					placeholder: "<?=t('Type to search')?>", // The search input placeholder
					perPage: "<?=t('{select} per-page')?>", // per-page dropdown label
					noRows: "<?=t('No newsletters')?>", // Message shown when there are no search results
					info: "<?=t('Showing {start} to {end} of {rows} letters')?>", //Showing {start} to {end} of {rows} entries
				},
				// Customise the layout
				layout: {
					top: "{select}{search}",
					bottom: "{info}{pager}"
				}
			});
			dataTable.on('datatable.sort',function(column, direction, init){
				if(this.initialized){
					gu_memo('gu_sort_col',1+column);
					gu_memo('gu_sort_dir',direction);
				}
			});
			dataTable.on('datatable.page',function(page){gu_memo('gu_page',page);});
			dataTable.on('datatable.search',function(query, matched){gu_memo('gu_search',query,!query);});
			dataTable.on('datatable.perpage',function(perpage){gu_memo('gu_perpage', perpage);});
			//dataTable.on('datatable.init',function(){});//init
			if(!all){
				 window.setTimeout(function() {
					memo_sort_col = gu_memo('gu_sort_col');
					memo_sort_dir = gu_memo('gu_sort_dir');
					if(memo_sort_col){
						//Fix init sort desc, need 'asc' to be good ::: if direction == "asc"
						var dir = memo_sort_dir == 'descending'?'asc':memo_sort_dir;
						dataTable.columns().sort(parseInt(memo_sort_col), dir,!0);
					}
					memo_page = gu_memo('gu_page');
					if(memo_page){// = gu_memo('gu_page');//get
						if(dataTable.pages.length >= memo_page)
							dataTable.page(memo_page);
						else gu_memo('gu_page',0,1);//unset
					}
					//Fix? Uncaught TypeError: query is null
					memo_search = gu_memo('gu_search');
					//Fix Uncaught TypeError: memo_search is null
					memo_search = memo_search?memo_search.trim():memo_search;
					if(memo_search){
						try{// js to add str in input search
							var dtSearch = document.querySelector('input.dataTable-input');//ie 8 min : developer.mozilla.org/en-US/docs/Web/API/Document/querySelector
							dtSearch.value = memo_search;
						}catch(e){// legacy : if NO querySelector Funk
							var dtSearch = document.getElementsByTagName('input');
							for(var dti = 0; dti < dtSearch.length; dti++){
								if(dtSearch[dti].className=='dataTable-input'){
									dtSearch[dti].value = memo_search;
									break;
								}
							}
						}
						dataTable.search(memo_search);//use proto search
					}//memo_search
				}, 161);
			}//!all
		}
	}

	function gu_newsletters_autobatch_checked(){//!!!!unused!!!! tep by move
		//idNew[]
		var lists = document.getElementsByName('idNew[]');
		var ok, ck;
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
		if(!ck) {gu_allin_anim();return;}
		if(!ok) return;
		var send_id = ids.shift();//ids[0] && remove 1st elem
		var batch_time_limit = <?php echo $batch_time_limit ?>;
		var outboxcount = parseInt(document.getElementById('mailbox_outbox_count').innerHTML);
		var outboxcheck = ids.join('O');

		window.location.href = 'newsletters.php?box=outbox&send=' + send_id + '&auto=' + batch_time_limit + '&count=' + outboxcount + '&check=' + outboxcheck;
	}
// why not #tep (outbox & drafts)
	function gu_newsletters_move_autobatch_checked(){
		var lists = document.getElementsByName('idNew[]');
		var ok, ck, r = 0;
		var ids = [];
		var cln = '';
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
		if(!ck) {gu_allin_anim();return;}
		if(!ok) return;
		//~ gu_success('1ere salve en cours, patience...');//todo trad
		//~ gu_messages_display(0);
		var urlParams = new URLSearchParams(window.location.search);
		r = (urlParams.get('box') == 'drafts'?333:0);
		if(r){//drafts
			gu_success('Praparatif en cours, patience...');//todo trad
			gu_messages_display(0);
			gu_newsletter_ajax_post(ids.join('O'), 1111, 'moves', 'na');//goto outbox AllInOneTime
		}
		var count = ids.length;
		var rtime = r*count;//r?r*count:333;
		//~ var send_id = ids.shift();//ids[0] && remove 1st elem
		var send_id = ids[0];
		var batch_time_limit = <?php echo $batch_time_limit ?>;
		var check = ids.join('O');
		var u = 'newsletters.php?box=outbox&send=' + send_id + '&auto=' + batch_time_limit + '&count=' + count + '&check=' + check;
		count = 0 + parseInt(document.getElementById('mailbox_outbox_count').innerHTML) + count;
		setTimeout("window.location.href = '"+u+"';",rtime);
		gu_success('Envois en Cours, patience...');//todo : trad
		gu_messages_display(2222);
		setTimeout("setMsge('gu_statusmsg','mvto')",2345);//tep
		document.querySelector('thead .icon_send_auto').classList.add('animate');//animate
	}
	actAllNew = false;//global
	function gu_newsletter_deletes(){
		var txt = "<?php echo t('Are you sure you want to delete selected newsletters?');?>"
		gu_newsletters_all('deletes', txt, 1);//Call All by One
	}
	function gu_newsletter_moves(){
		var txt = "<?php echo t('Are you sure you want to move selected newsletters?');?>"
		gu_newsletters_all('moves', txt, 1);//Call All by One
	}
	function gu_newsletters_delete(){//unused
		var txt = "<?php echo t('Are you sure you want to delete selected newsletter?');?>"
		gu_newsletters_all('delete', txt, 0);//Call One by One
	}
	function gu_newsletters_move(){//unused
		var txt = "<?php echo t('Are you sure you want to move selected newsletter?');?>"
		gu_newsletters_all('move', txt, 0);//Call One by One
	}
	function gu_newsletters_all(act, txt, multi){
		var lists = document.getElementsByName('idNew[]');
		var ok, ck, fadeTime = 0;
		var aIds = [];
		var one = !multi;
		for(var i = 0; i < lists.length; i++){
			if(lists[i].checked){
				ck = !0;
				//Call One by One
				if(one){
					if(!ok){
						ok = confirm(txt);
						if(!ok) return;//only one time ;)
					}
					setTimeout('gu_newsletter_' + act + '("' + lists[i].value + '", "222")', fadeTime);//gu_newsletter_delete(list_id)
					fadeTime = fadeTime + 444;
				}else{//multi
					ok = !0;
					gu_element_set_background("row_" + lists[i].value, '#DDFFDD');//tep
				}
				aIds.push(lists[i].value);
			}
		}
		//Check before anim
		if(!ck) {gu_allin_anim();return;}
		//Call All by One
		if(multi&&ok){
			fadeTime = 1000;
			setTimeout('gu_newsletters("' + act + '","' + txt + '", "' + aIds.join('O') + '", "1000")', fadeTime);//gu_newsletter_delete(list_id)
		}
	}
	function gu_newsletters(act, txt, id, fadeTime){//ajax_post Call 4 All in one by one call
		var aIds = id.split('O');
		if(!gu_newsletter_ajax_post(id, fadeTime, act, txt)){
			for(var i = 0; i < aIds.length; i++){
				gu_element_set_background("row_" + aIds[i], '');//unset
			}
		}
	}

	function gu_newsletter_msg(id, fadeTime){//ajax_post Call one by one
		var txt = id;//"<?php #echo t('Are you sure you want to show this newsletter?');?>";
		gu_newsletter_ajax_post(id, 222, 'msg', txt);
	}

	function gu_newsletter_delete(id, fadeTime){//ajax_post Call one by one
		var txt = "<?php echo t('Are you sure you want to delete this newsletter?');?>";
		gu_newsletter_ajax_post(id, fadeTime, 'delete', txt);
	}
	function gu_newsletter_move(id, fadeTime, txt){//ajax_post Call one by one
		gu_newsletter_ajax_post(id, fadeTime, 'move', txt);
	}
	function gu_newsletter_ajax_post(id, fadeTime, act, txt){
		var fadeTime = fadeTime?fadeTime:1000;
		var all = !(fadeTime == 1000);
		if (all || confirm(txt)){
			gu_messages_clear();
			actAllNew = all;/* global */
			var mysack = new sack("<?php echo absolute_url('ajax.php'); ?>");
			mysack.execute = 1;
			mysack.method = "POST";
			mysack.setVar("action", "newsletter_" + act);
			mysack.setVar("newsletter", id);
			mysack.onError = function(){ gu_error("<?php echo t('An error occured whilst making AJAX request');?>"); gu_messages_display(0); };
			mysack.onCompletion = function(){ gu_messages_display(fadeTime); }
			mysack.runAJAX();
			return mysack.response;
		}
		return false;
	}
	/* Set the content of popup css and open it */
	function gu_ajax_on_newsletter_msg(id,html){
		var boxmsg = document.getElementById('gu_ajax_on_newsletter_msg');
		//stackoverflow.com/a/3431528
		boxmsg.innerHTML = decodeURIComponent(html.replace(/\+/g, ' '));
		window.location.hash = '#pop_msg';
	}
	function gu_ajax_on_newsletter_delete(id){
		var fadeTime = actAllNew?222:1000;//fix for multiple
		var count = parseInt(document.newsletters_form.num_newsletters.value) - 1;
		document.newsletters_form.num_newsletters.value = count;
		gu_element_set_inner_html("mailbox_<?php echo $box; ?>_count", count);
		//datatable   github.com/Mobius1/Vanilla-DataTables/wiki/rows()
		if(!actAllNew){
			if(dataTable && dataTable.initialized){
				var rowToRemove = dataTable.body.querySelector("#row_" + id);// Get dataindex by id row
				if(rowToRemove && (rowToRemove.dataIndex > -1)){// ok Remove it
					dataTable.rows().remove(rowToRemove.dataIndex);
					var memo_search = gu_memo('gu_search');//get
					if(memo_search && memo_search.trim() != ''){// = gu_memo('gu_search');//get
						dataTable.search(memo_search);
					}
				}
			}
			else document.getElementById("row_" + id).remove();//vanilla
		}
		actAllNew = false;//global
	}
	function gu_ajax_on_newsletter_move(id){
		var fadeTime = actAllNew?222:1000;//fix for multiple
		var count = parseInt(document.newsletters_form.num_newsletters.value) - 1;
		var tep = document.getElementById("mailbox_<?php echo $xob; ?>_count");
		var tnuoc = 1+parseInt(tep.innerHTML);
		document.newsletters_form.num_newsletters.value = count;
		gu_element_set_inner_html("mailbox_<?php echo $box; ?>_count", count);
		gu_element_set_inner_html("mailbox_<?php echo $xob; ?>_count", tnuoc);
		if(!actAllNew){
			if(dataTable && dataTable.initialized){
				var rowToRemove = dataTable.body.querySelector("#row_" + id);// Get dataindex by id row
				if(rowToRemove && (rowToRemove.dataIndex > -1)){// ok Remove it if index > 0 :: fix v1
					dataTable.rows().remove(rowToRemove.dataIndex);
					var memo_search = gu_memo('gu_search');//get
					if(memo_search && memo_search.trim() != ''){
						dataTable.search(memo_search);
					}

				}
			}
			else document.getElementById("row_" + id).remove();//vanilla
		}
		actAllNew = false;//global
	}
	function gu_ajax_on_newsletter_moves(ids,nos){
		gu_ajax_on_newsletter_acts(ids,nos,'move');
	}
	function gu_ajax_on_newsletter_deletes(ids,nos){
		gu_ajax_on_newsletter_acts(ids,nos,'delete');
	}
	function gu_ajax_on_newsletter_acts(ids,nos,act){
		var aIds = nos.split('O');
		for(var i = 0; i < aIds.length; i++){
			if(!aIds[i]) continue;
			gu_element_set_background("row_" + aIds[i], '#FFDDDD');//DCD
			//inform by title
			document.getElementById("row_" + aIds[i]).title = "<?php echo strip_tags(ERROR_EXTRA); ?>/"+aIds[i];//vanillaTitle
		}
		var aIdsOK = new Array();
		aIds = ids.split('O');
		var dtOk = dataTable && dataTable.initialized;
		for(var i = 0; i < aIds.length; i++){
			if(!aIds[i]) continue;
			actAllNew=true;//fix for multiple
			if(act=='move')gu_ajax_on_newsletter_move(aIds[i]);
			else gu_ajax_on_newsletter_delete(aIds[i]);

			if(dtOk){
				var rowToRemove = dataTable.body.querySelector("#row_" + aIds[i]);// Get dataindex by id row
				if(rowToRemove && (rowToRemove.dataIndex > -1)){// ok Remove it if index > 0 :: fix v1
					aIdsOK.push(rowToRemove.dataIndex);
				}
			}
			else document.getElementById("row_" + aIds[i]).remove();//vanilla
		}

		if(dtOk){
			dataTable.rows().remove(aIdsOK);//tep by array
			var memo_search = gu_memo('gu_search');//get
			if(memo_search && memo_search.trim() != ''){

				dataTable.search(memo_search);
			}
		}else
		gu_load_DataTable();//yep + memo gu_search
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
			setMsge('gu_statusmsg','mvto');//tep
			links = false;
		}
		if(timeleft <= 1){
			gu_success('Envois en Cours, patience...');//todo : trad
			gu_messages_display();
		}
		if(timeleft <= 0){
			txt = clr;
			setMsge('gu_statusmsg','mvto');//tep
		}else{
			txt = timeleft + 's';
		}
		document.getElementById('countdown').innerHTML = '#AutoBatch ' + txt;
		document.getElementById('progressBar').value = totaltime - timeleft;
		timeleft -= 1;
		if(txt == clr)
			clearInterval(sendTimer);
	}, 1000);
<?php endif;#$auto_send ?>
/* ]]> */
</script>

<!-- <a href="#pop_msg">acces it (open pop)</a> -->
 <div id="pop_msg" class="overlay"><?php #pop css only ?>
  <div class="popup">
   <a class="close" title="Fermer cette popup" href="#noinfo">&times;</a>
   <div class="content" id="gu_ajax_on_newsletter_msg"><h2>#msg #html</h2></div>
  </div>
 </div><?php #fi pop css only ?>

<?php
include_once 'themes/'.gu_config::get('theme_name').'/_newsletters.php';//Body
gu_theme_end();