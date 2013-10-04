<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Subscription functions
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

/**
 * Subscribes or unsubscribes the specified address from the given lists
 * @param string $address The address to subscribe or unsubscribe
 * @param array $list_ids An array of IDs of the lists
 * @param bool TRUE if address should be subscribed, else FALSE if address should be unsubscribed
 * @return bool TRUE if operation was successful, else FALSE
 */
 
include_once 'inc/mailer.php';

/**
 * Processes (un)subscription requests to multiple lists
 * @param string $address The email address
 * @param array $list_ids The ids of the lists
 * @param bool $subscribe TRUE if addresss should be subscribed to the lists, FALSE if it should be unsubscribed
 * @return bool TRUE if operation was successful, else FALSE
 */
function gu_subscription_process($address, &$list_ids, $subscribe)
{
	if (!check_email($address))
		return gu_error(t("Invalid email address"));
		
	$succ_list_names = array();
	$fail_list_names = array();
	
	// For each list we need to load it with all addresses
	foreach ($list_ids as $list_id) {
		$list = gu_list::get($list_id, TRUE);
		
		// Don't allow subscriptions to private lists
		if ($list->is_private())
			$res = FALSE;
		else {	
			if ($subscribe)
				$res = $list->add($address, TRUE);
			else
				$res = $list->remove($address, TRUE);
		}
	
		if ($res)
			$succ_list_names[] = $list->get_name();
		else
			$fail_list_names[] = $list->get_name();
	}
	
	// Check if there were any successful 
	if (count($succ_list_names) < 1)
		return FALSE;
		
	// Work out if we need to send any emails now, and if so create a sender
	if (gu_config::get('list_send_welcome') || gu_config::get('list_send_goodbye') || gu_config::get('list_subscribe_notify') || gu_config::get('list_unsubscribe_notify')) {
		$mailer = new gu_mailer();
		if ($mailer->init()) {
			$subject_prefix = (count($succ_list_names) == 1) ? $succ_list_names[0] : gu_config::get('collective_name');
			
			// Send welcome / goodbye message
			if (($subscribe && gu_config::get('list_send_welcome')) || (!$subscribe && gu_config::get('list_send_goodbye'))) {		 
				$subject = '['.$subject_prefix.'] '.($subscribe ? t('Subscription') : t('Unsubscription')).t(' confirmation');
				$action = ($subscribe ? t('subscribed to') : t('unsubscribed from'));
				$text = t("This is an automated message to confirm that you have been % the following lists:",array($action))."\n\n* ".implode("\n* ", $succ_list_names)."\n\n";
				$text .= t('To change your subscriptions visit: ').absolute_url('subscribe.php').'?addr='.$address."\n\n";
				$text .= t('Please do not reply to this message. Thank you.');
				
				$mailer->send_mail($address, $subject, $text);
			}
			
			// Send admin notifications
			if (($subscribe && gu_config::get('list_subscribe_notify')) || (!$subscribe && gu_config::get('list_unsubscribe_notify'))) {
				$subject = '['.$subject_prefix.'] '.($subscribe ? t('Subscription') : t('Unsubscription')).t(' notification');
				$action = ($subscribe ? t('subscribed to') : t('unsubscribed from'));
				$text = t("This is an automated message to notify you that % has been % the following lists:",array($address,$action))."\n\n* ".implode("\n* ", $succ_list_names)."\n\n";
				
				$mailer->send_admin_mail($subject, $text);
			}
		}	
	}

		$action = ($subscribe ? t('subscribed to') : t('unsubscribed from'));
	return gu_success(t('You have been % lists: <i>%</i>', array($action,implode('</i>, <i>', $succ_list_names))));	
}

?>