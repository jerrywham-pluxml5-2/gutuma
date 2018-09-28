<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Subscription functions
 * @modifications Cyril Maguire, Thomas Ingles
 *
 * Gutama plugin package
 * @version 2.0.0
 * @date	23/09/2018
 * @author	Cyril MAGUIRE, Thomas Ingles
 *
 * Subscribes or unsubscribes the specified address from the given lists
 * @param string $address The address to subscribe or unsubscribe
 * @param array $list_ids An array of IDs of the lists
 * @param bool TRUE if address should be subscribed, else FALSE if address should be unsubscribed
 * @param str '' | 'i' : double opt-in/out
 * Send Mails to client & admin
 * @return bool TRUE if operation was successful, else FALSE
*/
include_once 'inc/mailer.php';
/**
 * Processes (un)subscription requests to multiple lists
 * @param string $address The email address
 * @param array $list_ids The ids of the lists
 * @param bool $subscribe TRUE if addresss should be subscribed to the lists, FALSE if it should be unsubscribed
 * @param string $hash '' send or 0000 resend mail validation key. if key.len == 40 test validate & is ok add/remove from real lists
 * @return bool TRUE if operation was successful, else FALSE
 */
function gu_subscription_process($address, &$list_ids, $subscribe, $hash = ''){
	if(!check_email($address))
		return gu_error(t("Invalid email address",null,$slang));
	$succ_list_names = array();
	$fail_list_names = array();
	$keycodetext = '';
	$sandy = FALSE;
	$sendkey = is_post_var('send_k');//FALSE;
	$resendHash = ($hash=='0000' OR $sendkey);//bep idea
	if($resendHash) $hash='';
	gu_debug('gu_subscription_process()  address: '.$address.', list_ids: '.implode(', ', $list_ids).', subscribe: '.($subscribe ? 'TRUE' : 'FALSE').', hash key size: '.strlen($hash));
	$status_msg =''; //
//	loop to transfert validated act
	foreach($list_ids as $list_id){// For each list we need to load it with all addresses
		$resendHash = FALSE;
		$res = FALSE;
		$list = gu_list::get($list_id, TRUE);//real list
		if($list){//!= false (object)
			$firstTime = $lastTime = FALSE;//init return msg
			if($list->is_private())// Don't allow subscriptions to private lists)
				$res = FALSE;
			else {
				$listi = gu_list::get($list_id, TRUE, 'i');//temporary in/out list
				$intemp = $listi->contains($address,TRUE);//before ops
				$resendHash = (!$hash AND $intemp AND $sendkey)?TRUE:$resendHash;//veta test
				$valtime = gu_config::get('days')*24*60*60;//keycode valid time
				if($resendHash AND $listi->get_size()) {//veta test
					$keycode = $listi->get_tmp_key($address);//bep...
					$res = !!$keycode;//isset($listi->timeAddress)?TRUE:FALSE;
					if($res) $keycodetext .= "\n\n".t('To validate your opération (%) for list "%" visit: ',array(strtolower(($subscribe ? t('Subscribe') : t('Unsubscribe'))), $list->get_name()))."\n".absolute_url('subscribe.php').'?addr='.$address.'&list='.$list->get_id().'&action='.($subscribe ?'':'un').'subscribe&k='.$keycode."\n\n".t('Or paste this text in "key code" field before clic on % button: ',array(($subscribe ? t('Subscribe') : t('Unsubscribe'))))."\n\n".$keycode."\n\n".t('Use this link or keycode before %, after you need a new keycode.',array(date(t('Y-m-d H:i'),($listi->timeAddress + $valtime))))."\n".str_repeat("-",72)."\n\n";//
				}elseif(!$sendkey AND $hash AND $ok = $listi->contains($address, TRUE, $hash)) {//good key (real add)
					$lastTime = TRUE;
					if($subscribe){
						$res = $listi->remove($address, TRUE, 'i', $hash);//remove in tmp
						$res = $res AND $list->add($address, TRUE, '', $hash);//add in real
					}else{
						$res = $listi->remove($address, TRUE, 'i', $hash);//remove in tmp
						$res = $res AND $list->remove($address, TRUE, '', $hash);//remove in real
					}
				}else{//first time
					if(!$sendkey){
						$firstTime = TRUE;
						if($subscribe){
							$res = ((!$list->contains($address)) && (!$listi->contains($address,TRUE)));
							$res = $res AND $listi->add($address, TRUE, 'i');//tmp add + time to create hash
						}else{
							$res = (($list->contains($address)) && (!$listi->contains($address,TRUE)));
							$res = $res AND $listi->add($address, TRUE, 'i');//tmp remove + time to create hash
						}
					}
					if($res OR ($resendHash AND $sendkey)){//send text to validate 1st (un)subscrition
						$sandy = TRUE;//Fix $sendkey switch (only one) .... is_post_var('send_k');//is_get_var('send_k');
						$keycode = $listi->get_tmp_key($address);
						$keycodetext .= "\n\n".t('To validate your opération (%) for list "%" visit: ',array(strtolower(($subscribe ? t('Subscribe') : t('Unsubscribe'))), $list->get_name()))."\n".absolute_url('subscribe.php').'?addr='.$address.'&list='.$list->get_id().'&action='.($subscribe ?'':'un').'subscribe&k='.$keycode."\n\n".t('Or paste this text in "key code" field before clic on % button: ',array(($subscribe ? t('Subscribe') : t('Unsubscribe'))))."\n\n".$keycode."\n\n".t('Use this link or keycode before %, after you need a new keycode.',array(date(t('Y-m-d H:i'),($listi->timeAddress + $valtime))))."\n".str_repeat("-",72)."\n\n";
					}
					$status_msg .= '<br /><b><i>'.t(($list->contains($address)?'':'un').'subscribed').'</i></b> '.t('in').' <b>"'.$list->get_name().'"</b> '.t('and its key code is').' <b><i>'.t(($listi->contains($address,TRUE)?'present':'ungenerated')).'.</i></b>';
				}//FI first time
			}//FI Public list
			$isList = ($res?' ('.($firstTime?t('temporary'):($lastTime?t('real'):'')).')':'');//add temporary/real for msg
			$isList = '<br />'.$list->get_name().$isList;
			if($res)
				$succ_list_names[] = $isList;//$list->get_name();
			else
				$fail_list_names[] = $isList;//$list->get_name();
		}
	}//fi foreach lists
	if ($status_msg AND !$sandy)// Check if there were any temporary
		$_SERVER['GU_STATUS_MSG'] = t('State of email <b>"%"</b> is: ',array($address)) . $status_msg;//$_SERVER['GU_ERROR_MSG'] GU_SUCCESS_MSG gu_success()
	$succ_list_count = count($succ_list_names);
	if ($succ_list_count < 1)// Check if there were any successful
		return FALSE;
	$plural = ($succ_list_count > 1);//multiple lists
	$succ_list_search = array('<br />',' (',')',t('temporary'),t('real'));//for remove msg text (HTML & more added by $isList)
	$succ_list_text = str_replace($succ_list_search,'',implode("\n* ", $succ_list_names));//in mails
//	Work out if we need to send any emails now, and if so create a sender
	$mailer = new gu_mailer();
	if($mailer->init()){
		$subject_prefix = (count($succ_list_names) == 1) ? str_replace($succ_list_search,'',$succ_list_names[0]) : gu_config::get('collective_name');
//		(Re)Send hash key code messages :)
		if ($sendkey OR $sandy){//send mail with validate key code and user notice : 1st time or unsub
			$subject = '['.$subject_prefix.'] '.($subscribe ? t('Subscription') : t('Unsubscription')).t(' validation key');# Validate key code
			$action = strtolower(($subscribe ? t('Subscription') : t('Unsubscription')));
			$text = t('This is an automated message');
			$text .= t(' to send key code for validate % operation on the following list'.($plural?'s':'').':',array($action))."\n\n* ".$succ_list_text."\n\n";
			$text .= "\n".str_repeat('-',72).$keycodetext."\n\n".t('To change your subscription'.($plural?'s':'').' visit: ')."\n".absolute_url('subscribe.php').'?addr='.$address.((count($succ_list_names) == 1) ? "&list=".$list_id : "")."\n\n";
			$text .= t('Please do not reply to this message. Thank you.');
//gu_debug('gu_subscription_process() sendkey message to: '.$address.'<br /> subject: '.$subject.'<br />text: '.$text);
//echo '<pre>subject : '.$subject.PHP_EOL.$text.'</pre>';#dbg
			$mailer->send_mail($address, $subject, $text);
			return gu_success(($subscribe ? t('Subscription') : t('Unsubscription')).', '.t('first step successful!').'<br />'.t('Your key code sended for list'.($plural?'s':'').': <i>%</i>', array(implode('</i>, <i>', $succ_list_names))).'<br /><br /><i>'.t('Remember to <b>check in spam mails</b> if the messages are not found in your inbox').'.</i>');
		}#FI 1st time or unsub
		else if (gu_config::get('list_send_welcome') || gu_config::get('list_send_goodbye') || gu_config::get('list_subscribe_notify') || gu_config::get('list_unsubscribe_notify')){
//			Send welcome / goodbye message
			if (($subscribe && gu_config::get('list_send_welcome')) || (!$subscribe && gu_config::get('list_send_goodbye'))){
				$subject = '['.$subject_prefix.'] '.($subscribe ? t('Subscription') : t('Unsubscription')).t(' confirmation');
				$action = ($subscribe ? t('subscribed to') : t('unsubscribed from'));
				$text = t('This is an automated message').t(' to confirm that you have been % the following list'.($plural?'s':'').':',array($action))."\n\n* ".$succ_list_text."\n\n";
				$text .= t('To change your subscription'.($plural?'s':'').' visit: ').absolute_url('subscribe.php').'?addr='.$address.((count($succ_list_names) == 1) ? "&list=".$list_id : "")."\n\n";
				$text .= t('Please do not reply to this message. Thank you.');
//gu_debug('gu_subscription_process() Send welcome / goodbye message to: '.$address.'<br /> subject: '.$subject.'<br />text: '.$text);
//echo '<pre>subject : '.$subject.PHP_EOL.$text.'</pre>';#dbg
				$mailer->send_mail($address, $subject, $text);
			}
// Send admin notifications
			if (($subscribe && gu_config::get('list_subscribe_notify')) || (!$subscribe && gu_config::get('list_unsubscribe_notify'))){
				$subject = '['.$subject_prefix.'] '.($subscribe ? t('Subscription') : t('Unsubscription')).t(' notification');
				$action = ($subscribe ? t('subscribed to') : t('unsubscribed from'));
				$text = t('This is an automated message').t(' to notify you that % has been % the following list'.($plural?'s':'').':',array($address,$action))."\n\n* ".$succ_list_text."\n\n";
//gu_debug('gu_subscription_process() Send admin notifications subject: '.$subject.'<br />text: '.$text);
//echo '<pre>'.$subject.PHP_EOL.$text.'</pre>';
				$mailer->send_admin_mail($subject, $text);
			}
		}
	}
	$action = (($subscribe) ? t('subscribed to') : t('unsubscribed from'));//TODO : Vous allez vous (dés)abonné de
	if(!$sendkey)//fix (tep) message if is in tmp list & clic on send me key
		return gu_success(t('You have been % list'.($plural?'s':'').': <i>%</i>', array($action,implode('</i>, <i>', $succ_list_names))).'<br /><br /><i>'.t('Remember to <b>check in spam mails</b> if the messages are not found in your inbox').'.</i>');
}
