<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The AJAX interface to Gutuma
 * @modifications Cyril Maguire, thomas Ingles
 *
 * Gutama plugin package
 * @version 2.1.0
 * @date	01/10/2018
 * @author	Cyril MAGUIRE, Thomas INGLES
*/
header('Content-Type: application/x-javascript; charset=utf-8');
//header('Content-Type: text/html; charset=utf-8');

include_once 'inc/gutuma.php';
include_once 'inc/subscription.php';
include_once 'inc/newsletter.php';

// Initialize Gutuma without validation or redirection
gu_init(FALSE, FALSE);

// Acceptable public action values, i.e ones that don't require a valid session
$public_actions = array('subscribe', 'unsubscribe');

// Get posted action var which determines which function gets called
if (!is_post_var('action'))
	gu_ajax_error(t('No action specified in AJAX request'));

$action = get_post_var('action');
$is_public_action = in_array($action, $public_actions);

// Check for valid session if not a public action
if (!gu_session_is_valid() && !$is_public_action)
	gu_ajax_error(t('This action requires a valid session. Try logging in again.'));

// Call the appropriate function
switch ($action) {
	case 'subscribe':
		$list = is_post_var('list') ? gu_list::get((int)get_post_var('list'), TRUE) : NULL;
		$address = is_post_var('address') ? get_post_var('address') : '';
		gu_ajax_subscribe($list, $address, TRUE);
		break;
	case 'unsubscribe':
		$list = is_post_var('list') ? gu_list::get((int)get_post_var('list'), TRUE) : NULL;
		$address = is_post_var('address') ? get_post_var('address') : '';
		gu_ajax_subscribe($list, $address, FALSE);
		break;
	case 'list_add':
		$name = is_post_var('name') ? trim(get_post_var('name')) : '';
		$private = is_post_var('private') ? (bool)get_post_var('private') : false;
		gu_ajax_list_add($name, $private);
		break;
	case 'list_delete':
		$list = is_post_var('list') ? gu_list::get((int)get_post_var('list'), TRUE) : NULL;
		gu_ajax_list_delete($list);
		break;
	case 'add_del_address':# In all/specified lists
		$address = is_post_var('address') ? get_post_var('address') : '';
		$lists = is_post_var('lists') ? explode('·', get_post_var('lists')) : FALSE;
		$add = is_post_var('add') ? TRUE : FALSE;
		gu_ajax_make_in_all_lists($address, $lists, $add);
		break;
	case 'remove_address':
		$tmp = is_post_var('tmp') ? get_post_var('tmp') : '';
		$list = is_post_var('list') ? gu_list::get((int)get_post_var('list'), TRUE, $tmp) : NULL;
		$address = is_post_var('address') ? get_post_var('address') : '';
		$address_id = is_post_var('address_id') ? (int)get_post_var('address_id') : 0;
		gu_ajax_remove_address($list, $address, $address_id, $tmp);
		break;
	case 'newsletter_delete':
		$newsletter = is_post_var('newsletter') ? gu_newsletter::get((int)get_post_var('newsletter')) : NULL;
		gu_ajax_newsletter_delete($newsletter);
		break;
}
// If action function hasn't already returned due to error, return now
gu_ajax_return();

/**
 * Called when an error has occured.
 * @param string $msg The error message to send to the client
 */
function gu_ajax_error($msg){
	gu_error($msg);
	gu_ajax_return();
}
/**
 * Returns the specified Javascript snippet to the client
 * @param string $script The Javascript snippet to return
 */
function gu_ajax_return($script = ''){
	global $is_public_action;
	if (!$is_public_action){// If its not a public action, return status and error message separately
		if (isset($_SERVER['GU_STATUS_MSG']))
			echo 'gu_success("'.addslashes($_SERVER['GU_STATUS_MSG']).'"); ';
		if (isset($_SERVER['GU_ERROR_MSG']))
			echo 'gu_error("'.addslashes($_SERVER['GU_ERROR_MSG']).'"); ';
	}
	die($script);
}
/**
 * Subscribes or unsubscribes the specified email address to/from the specified list
 * @param gu_list $list The list
 * @param string $address The email address
 * @param bool $subscribe TRUE if address should be subscribed, FALSE if it should be unsubscribed
 */
function gu_ajax_subscribe($list, $address, $subscribe = TRUE){
	if (!$list)
		return gu_ajax_return('alert("'.t('Invalid list').'")');
	$list_ids = array($list->get_id());
	if (!gu_subscription_process($address, $list_ids, $subscribe))
		gu_ajax_return('alert("'.addslashes(strip_tags(@$_SERVER['GU_STATUS_MSG'].' '.@$_SERVER['GU_SUCCESS_MSG'].' '.@$_SERVER['GU_ERROR_MSG'])).'")');
	$success = t('Thank you for your').' '.t('Subscription').' '.$address.', '.t('the first step is successful!').' '.t('You will receive one email to validate your % for following list: <i>%</i>', array(t('Subscription'),$list->get_friend())).'. '.t('Remember to <b>check in spam mails</b> if the message are not found in your <b>inbox</b>');
	gu_ajax_return('alert("'.strip_tags($success).'.")');
}
/**
 * Adds a new list with the specified name
 * @param string $name The name of the new list
 * @param bool $private TRUE if new list is private, else FALSE
 */
function gu_ajax_list_add($name, $private){
	if (($list = gu_list::create($name, $private)) != FALSE){
		gu_success(t('New list <b><i>%</i></b> added',array($list->get_name())));
		gu_ajax_return('gu_ajax_on_list_add('.$list->get_id().', "'.$list->get_name().'", '.($list->is_private() ? 'true' : 'false').')');
	}
}
/**
 * Deletes the specified list
 * @param gu_list $list The list to delete
 */
function gu_ajax_list_delete($list){
	if (!$list)
		return gu_ajax_error(t('Invalid list'));
	if ($list->delete()){
		gu_success(t('List <b><i>%</i></b> deleted',array($list->get_name())));
		gu_ajax_return('gu_ajax_on_list_delete('.$list->get_id().')');
	}
}
/**
 * Action = 'add_del_address' to Add / Delete the specified address in all / selected lists
 * @param string $address The address to add/delete on lists
 * @param array $alists selected Lists of gu_list to add/remove address (or FALSE to do in all lists)
 * @param bool $add : true add, false delete
 * return real size of lists if persons have (un)subscribe in interval time
 */
function gu_ajax_make_in_all_lists($address, $alists, $add = FALSE){
	if (!check_email($address))
		return gu_error(t('Invalid email address'));

	$tmp = 'i';
	$sizes = array();
	$adel = array();
	$count = array(0,0);
	while(true){
		$lists = gu_list::get_all(TRUE, TRUE, $tmp);#get_all($load_addresses = FALSE, $inc_private = TRUE, $tmp = '')
		foreach($lists as $list){
			if($alists AND !in_array($list->get_id(), $alists))
				continue;
			if($add){
				if(!$tmp){
					$count[1]++;#add +1 in total lists
				}
			}else{
				$count[1]++;#del +1 in total lists
			}
			#DEL
			if($list->contains($address, $tmp)){
				if(!$add){#del mode
					if($list->remove($address, TRUE, $tmp)){#
						$sizes[] = $list->get_size() . ':' . $list->get_id() . $tmp;#list_id#[i] : like : 123456789·123456789i·987654321i·****i·****···
						$count[0]++;# +1 in deleted
					}
				}else{#add mode
					if($tmp){
						$adel[] = $list->get_id();#save id on var to not insert on normal list····
					}
				}
			#ADD & !tmp
			}else{#not contain address
				if($add && !$tmp){#add mode
					if(!in_array($list->get_id(), $adel)){#not in tmp
						if ($list->add($address, TRUE, '', 'ADMIN_EDITLIST_FAKE_KEY')){#From editlist.php
							$sizes[] = $list->get_size() . ':' . $list->get_id();# . $tmp;#list_id#[i] : like : 123456789·123456789·987654321·****·****···
							$count[0]++;# +1 in added
						}
					}
				}
			}
		}
		if(!$tmp)#stop ;)
			break;
		$tmp = '';# loop one time #1st in transit ok & 2nd for normal
	}
	$ay = 'Address <b><i>%</i></b> '.($add?'added':'removed');
	gu_success(t($ay ,array($address)) . ' (' . $count[0] . ' / ' . $count[1] . ')');
	gu_ajax_return('gu_ajax_on_make_in_all_lists("'.$address.'", "'.implode('·', $sizes).'", "'. intval($add).'")');//
}
/**
 * Removes the specified address from the specified list
 * @param gu_list $list The list to modify
 * @param string $address The address to remove
 * @param int $address_id The id of the address - only used to reference a HTML element on the calling page
 */
function gu_ajax_remove_address($list, $address, $address_id, $tmp = ''){
	if (!$list)
		return gu_error(t('Invalid list'));
	if ($list->remove($address, TRUE, $tmp)){
		if (!$tmp){
			$listi = gu_list::get($list->get_id(), TRUE, 'i');
			if ($listi->contains($address, 'i', '0000'))
				$listi->remove($address, TRUE, 'i');
		}
		gu_success(t('Address <b><i>%</i></b> removed',array($address)));
		gu_ajax_return('gu_ajax_on_remove_address('.$address_id.')');
	}
}
/**
 * Deletes the specified newsletter
 * @param gu_newsletter $newsletter The newsletter to delete
 */
function gu_ajax_newsletter_delete($newsletter){
	if (!$newsletter)
		return gu_error(t('Invalid newsletter'));
	if ($newsletter->delete()){
		$subject = trim($newsletter->get_subject());
		gu_success(t('Newsletter deleted') . ' (' . (!empty($subject)? $subject: t('Empty subject')) . ')');#remove subject EOL
		gu_ajax_return('gu_ajax_on_newsletter_delete('.$newsletter->get_id().')');
	}
}