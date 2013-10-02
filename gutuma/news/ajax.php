<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The AJAX interface to Gutuma
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/


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
	case 'remove_address':
		$list = is_post_var('list') ? gu_list::get((int)get_post_var('list'), TRUE) : NULL;
		$address = is_post_var('address') ? get_post_var('address') : '';
		$address_id = is_post_var('address_id') ? (int)get_post_var('address_id') : 0;
		gu_ajax_remove_address($list, $address, $address_id);
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
function gu_ajax_error($msg)
{
	gu_error($msg);
	gu_ajax_return();
}

/**
 * Returns the specified Javascript snippet to the client
 * @param string $script The Javascript snippet to return
 */
function gu_ajax_return($script = '')
{
	global $is_public_action;
	
	// If its not a public action, return status and error message separately
	if (!$is_public_action) {
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
function gu_ajax_subscribe($list, $address, $subscribe = TRUE)
{
	if (!$list)
		return gu_ajax_return('alert("'.t('Invalid list').'")');
		
	$list_ids = array($list->get_id());
	
	if (!gu_subscription_process($address, $list_ids, $subscribe))
		gu_ajax_return('alert("'.addslashes(strip_tags($_SERVER['GU_ERROR_MSG'])).'")');
		
	gu_ajax_return('alert("'.($subscribe ? t('Subscription') : t('Unsubscription')).t(' successful!').'")');
}

/**
 * Adds a new list with the specified name
 * @param string $name The name of the new list
 * @param bool $private TRUE if new list is private, else FALSE 
 */
function gu_ajax_list_add($name, $private)
{
	if (($list = gu_list::create($name, $private)) != FALSE) {
		gu_success(t('New list <b><i>%</i></b> added',array($list->get_name())));
		gu_ajax_return('gu_ajax_on_list_add('.$list->get_id().', "'.$list->get_name().'", '.($list->is_private() ? 'true' : 'false').')');
	}
}

/**
 * Deletes the specified list
 * @param gu_list $list The list to delete
 */
function gu_ajax_list_delete($list)
{
	if (!$list)
		return gu_ajax_error(t('Invalid list'));
		
	if ($list->delete()) {
		gu_success(t('List <b><i>%</i></b> deleted',array($list->get_name())));
		gu_ajax_return('gu_ajax_on_list_delete('.$list->get_id().')');
	}		
}

/**
 * Removes the specified address from the specified list
 * @param gu_list $list The list to modify
 * @param string $address The address to remove 
 * @param int $address_id The id of the address - only used to reference a HTML element on the calling page
 */
function gu_ajax_remove_address($list, $address, $address_id)
{
	if (!$list)
		return gu_error(t('Invalid list'));
		
	if ($list->remove($address, TRUE)){
		gu_success(t('Address <b><i>%</i></b> removed',array($address)));
		gu_ajax_return('gu_ajax_on_remove_address('.$address_id.')');
	}
}

/**
 * Deletes the specified newsletter
 * @param gu_newsletter $newsletter The newsletter to delete
 */
function gu_ajax_newsletter_delete($newsletter)
{
	if (!$newsletter)
		return gu_error(t('Invalid newsletter'));
		
	if ($newsletter->delete()) {
		gu_success(t('Newsletter deleted'));
		gu_ajax_return('gu_ajax_on_newsletter_delete('.$newsletter->get_id().')');
	}		
}

?>