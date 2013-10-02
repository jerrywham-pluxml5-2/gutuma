<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The settings page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/


include_once 'inc/gutuma.php';

if (isset($_GET['token'])) {
	list($name,$username,$password,$salt,$userProfile,$id,$new_record) = explode('[::]',unserialize(base64_decode($_GET['token'])));
	list($user_name,$user_login,$user_password,$user_salt,$user_userProfile,$user_id) = explode('[::]',unserialize(base64_decode($new_record)));
	$user_salt = substr($user_salt, 1,-2);
} else {
	header('Location:./index.php');
}

gu_init();

$users = gu_config::getUsers();
if (isset($users[$user_name])) {
	header('location:'.str_replace('plugins/gutuma/news','core/admin',absolute_url('plugin.php?p=gutuma&rec=done&u='.$users[$user_name]['id'])));
} 

// Save settings
if (is_post_var('save_settings')) {
	if (isset($users[$user_name])) {
		gu_error(t('<span style="color:red;">User already exists !</span>'));
	} else {
		gu_config::setUsers(
			get_post_var('id'),
			get_post_var('name'),
			get_post_var('login'),
			get_post_var('password'),
			base64_decode(get_post_var('salt')),
			get_post_var('userProfile')
		);
		if (gu_config::save())
			$ok = '';
			gu_success(t('New user successfully saved.'));

		
	}
}

gu_theme_start();

//Body
include_once 'themes/'.gu_config::get('theme_name').'/_users.php';

gu_theme_end();
?>
