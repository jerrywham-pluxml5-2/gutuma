<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Subscription functions
 * @modifications Cyril Maguire, Thomas Ingles
 *
 * Gutama plugin package
 * @version 2.1.0
 * @date	01/10/2018
 * @author	Cyril MAGUIRE, Thomas INGLES
 *
 * Subscribes or unsubscribes the specified address from the given lists
 * @param string $address The address to subscribe or unsubscribe
 * @param array $list_ids An array of IDs of the lists
 * @param bool TRUE if address should be subscribed, else FALSE if address should be unsubscribed
 * @param str '' | 'i' : double opt-in/out
 * Send Mails to client & admin
 * @return bool TRUE if operation was successful, else FALSE
*/
//~ include_once 'inc/mailer.php';//Origin
include_once str_replace('subscription.php','mailer.php',__FILE__);//if subscribe.php is include in other place with php include & normal ;)
/**
 * Processes (un)subscription requests to multiple lists
 * @param string $address The email address
 * @param array $list_ids The ids of the lists
 * @param bool $subscribe TRUE if addresss should be subscribed to the lists, FALSE if it should be unsubscribed
 * @param string $hash '' send mail security link. if key.len == 40 test validate & is ok add/remove from real lists
 * @return bool TRUE if operation was successful, else FALSE
 */
function gu_subscription_process($address, &$list_ids, $subscribe, $hash = ''){
	if(!check_email($address))
		return gu_error(t("Invalid email address",null,GU_CONFIG_LANG));//Fix Notice: Undefined variable: slang
	$EOL = "\r\n";//Fin de ligne (courriels)
	$HR = str_repeat("=",72);
	$succ_list_names = $fail_list_names = array();
	$keytext = $status_msg ='';
	$sendkey = FALSE;
	$what = ($subscribe ? t('Subscription') : t('Unsubscription'));
	gu_debug('gu_subscription_process()  address: '.$address.', list_ids: '.implode(', ', $list_ids).', subscribe: '.($subscribe ? 'TRUE' : 'FALSE').', hash key size: '.strlen($hash));
	$valtime = gu_config::get('days')*24*60*60;//keycode valid time
	$subscribe_url = gu_config::get('subscribe_url') != absolute_url('subscribe.php') ? gu_config::get('subscribe_url').'&' : absolute_url('subscribe.php').'?';//for php include change query param ::: Origin only absolute_url('subscribe.php')
//	loop to transfert validated act
	foreach($list_ids as $list_id){// For each list we need to load it with all addresses
		$res = FALSE;
		$list = gu_list::get($list_id, TRUE);//real list
		if($list){//!= false (object)
			$firstTime = $lastTime = FALSE;//init return msg
			if($list->is_private())// Don't allow subscriptions to private lists)
				$res = FALSE;
			else {
				$listi = gu_list::get($list_id, TRUE, 'i');//temporary in/out list
				$intemp = $listi->contains($address,TRUE);//before ops
				if($hash AND $ok = $listi->contains($address, TRUE, $hash)) {//Second time with good key (real add & rem)
					$lastTime = TRUE;
					if($subscribe){
						$res = $listi->remove($address, TRUE, 'i', $hash);//remove in tmp
						$res = $res AND $list->add($address, TRUE, '', $hash);//add in real
					}else{
						$res = $listi->remove($address, TRUE, 'i', $hash);//remove in tmp
						$res = $res AND $list->remove($address, TRUE, '', $hash);//remove in real
					}
				}else{//first time
					$firstTime = TRUE;
					if($subscribe){
						$res = ((!$list->contains($address)) && (!$listi->contains($address,TRUE)));
						$res = $res AND $listi->add($address, TRUE, 'i');//tmp add + time to create hash
					}else{
						$res = (($list->contains($address)) && (!$listi->contains($address,TRUE)));
						$res = $res AND $listi->add($address, TRUE, 'i');//tmp remove + time to create hash
					}
					if($res){//send text & link to validate 1st time of (un)subscrition
						$sendkey = TRUE;//For send security key
						$keycode = $listi->get_tmp_key($address);
						$keytext .= $EOL.t('To approve your % for list "%" visit:',array(strtolower($what), $list->get_friend())).' '.$EOL.$subscribe_url.'addr='.$address.'&list='.$list->get_id().'&action='.($subscribe ?'':'un').'subscribe&k='.$keycode.$EOL.t('Use this link (or copy in your browser address bar) before %, after this date, you need a request new valid link.',array(date(t('Y-m-d H:i'),($listi->timeAddress + $valtime)))).$EOL.$HR;
					}
					$status_msg .= '<br /><b><i>'.t(($list->contains($address)?'':'un').'subscribed').'</i></b> '.t('in').' <b>"'.$list->get_friend().'"</b> '.t('and').' <b><i>'.t(($listi->contains($address,TRUE)?'is awaiting a process':'has no pending requests')).'.</i></b>';//#msg : State of address for this list, used in following $_SERVER['GU_STATUS_MSG']
				}//FI first time
			}//FI Public list
			$isList = ($res?' ('.($firstTime?t('waiting a process'):($lastTime?t('process validated'):'')).')':'');//add temporary/real for #msg
			$isList = '<br />'.$list->get_friend().$isList;
			if($res)
				$succ_list_names[] = $isList;
			else
				$fail_list_names[] = $isList;
		}
	}//fi foreach lists
	if ($status_msg AND !$sendkey)// If no ops & have #msg, display it
		$_SERVER['GU_STATUS_MSG'] = t('Email <b>"%"</b> is:',array($address)) .' '. $status_msg;#msg : State of address
	$succ_list_count = count($succ_list_names);
	if ($succ_list_count < 1)// Check if there were any successful
		return FALSE;
	$plural = ($succ_list_count > 1);//multiple lists
	$succ_list_search = array('<br />',' (',')',t('waiting a process'),t('process validated'));//for remove msg text (HTML & more added by $isList)
	$succ_list_text = str_replace($succ_list_search,'',implode($EOL."* ", $succ_list_names));//in mails
//	Work out if we need to send any emails now, and if so create a sender
	$sendok = FALSE;//for display verify spam box #msg
	$mailer = new gu_mailer();
	if($mailer->init()){
		$subject_prefix = (count($succ_list_names) == 1) ? str_replace($succ_list_search,'',$succ_list_names[0]) : gu_config::get('collective_name');
		if ($sendkey){//send mail with security link and user notice : 1st time or unsub
			$sendok = TRUE;//for display verify spam box #msg
			$subject = '['.$subject_prefix.'] '.$what.' '.t('of').' '.$address.' - '.t('security link'.($plural?'s':''));# Validate key code
			$action = strtolower($what);
			$text = t('This is an automated message');
			$text .= t(' to secure the % and certify that you are the initiator of this process for the following list'.($plural?'s':'').':',array($action))."\r\n* ".$succ_list_text.$EOL;
			$text .= $EOL.str_repeat('=',72).$keytext."\r\n\r\n".t('To change your subscription'.($plural?'s':'').' visit:').' '.$EOL.$subscribe_url.'addr='.$address.((count($succ_list_names) == 1) ? '&list='.$list_id : '').$EOL;
			$text .= t('Please do not reply to this message. Thank you.');
			gu_debug('gu_subscription_process() sendkey message to '.$address.' :<br /> subject :<br />'.$subject.'<br />text :<br />'.$text);
//var_export('<pre>First step message : subject : '.$subject.PHP_EOL.$text.'</pre>');#dbg
			$mailer->send_mail($address, $subject, $text);
			return gu_success(t('Thank you for your').' &quot;'.$action.'&quot; '.$address.', '.t('the first step is successful!').'<br />'.t('You will receive one email to validate your % for following list'.($plural?'s':'').': <i>%</i>', array($action,implode('</i>, <i>', $succ_list_names))).'<br /><br /><i>'.t('Remember to <b>check in spam mails</b> if the message are not found in your <b>inbox</b>').'.</i>');
		}#FI 1st time or unsub
		else if (gu_config::get('list_send_welcome') || gu_config::get('list_send_goodbye') || gu_config::get('list_subscribe_notify') || gu_config::get('list_unsubscribe_notify')){
//			Send welcome / goodbye message
			if (($subscribe && gu_config::get('list_send_welcome')) || (!$subscribe && gu_config::get('list_send_goodbye'))){
				$sendok = TRUE;//for display #msg : verify spam box #msg
				$subject = '['.$subject_prefix.'] '.$what.' '.t('confirmed').' '.t('of').' '.$address;
				$action = ($subscribe ? t('subscribed'.($plural?'  ':' ').'to') : t('unsubscribed'.($plural?'  ':' ').'from'));
				$text = t('This is an automated message').t(' to confirm that you have been % the following list'.($plural?'s':'').':',array(str_replace('  ',' ',$action)))."\r\n* ".$succ_list_text.$EOL;
				$text .= $EOL.t('To change your subscription'.($plural?'s':'').' visit:').' '.$EOL.$subscribe_url.'addr='.$address.((count($succ_list_names) == 1) ? '&list='.$list_id : '').$EOL;
				$text .= t('Please do not reply to this message. Thank you.');
				gu_debug('gu_subscription_process() Send welcome / goodbye message to : '.$address.'<br /> subject :<br />'.$subject.'<br />text :<br />'.$text);
//echo '<pre>Welcome / goodbye message : subject : '.$subject.PHP_EOL.$text.'</pre>';#dbg
				$mailer->send_mail($address, $subject, $text);
			}
//			Send admin notifications
			if (($subscribe && gu_config::get('list_subscribe_notify')) || (!$subscribe && gu_config::get('list_unsubscribe_notify'))){
				$subject = '['.$subject_prefix.'] '.$what.' '.t('of').' '.$address.' - '.t('notification');
				$action = ($subscribe ? t('subscribed'.($plural?'  ':' ').'to') : t('unsubscribed'.($plural?'  ':' ').'from'));
				$text = t('This is an automated message').t(' to notify you that % has been % the following list'.($plural?'s':'').':',array($address,str_replace('  ',' ',$action)))."\r\n* ".$succ_list_text.$EOL;
				gu_debug('gu_subscription_process() Send admin notifications :<br /> subject :<br />'.$subject.'<br />text :<br />'.$text);
//echo '<pre>admin notification : subject : '.$subject.PHP_EOL.$text.'</pre>';//exit;#dbg
				$mailer->send_admin_mail($subject, $text);
			}
		}
	}
	$action = (($subscribe) ? t('subscribed'.($plural?'  ':' ').'to') : t('unsubscribed'.($plural?'  ':' ').'from'));//TODO : Vous allez vous (dés)abonné de
	return gu_success(t('You have been % list'.($plural?'s':'').': <i>%</i>', array(str_replace('  ',' ',$action),implode('</i>, <i>', $succ_list_names))).($sendok?'<br /><br /><i>'.t('Remember to <b>check in spam mails</b> if the message are not found in your <b>inbox</b>').'.</i>':''));
}