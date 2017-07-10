<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The login page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

include 'inc/gutuma.php';

gu_init(FALSE);

if (is_get_var('action') && get_get_var('action') == 'plxlogin') {;
	$name = is_post_var('n') ? get_post_var('n') : '';
	$username = is_post_var('u') ? get_post_var('u') : '';
	$password = is_post_var('p') ? get_post_var('p') : '';
	$remember = true;
	$user = false;
	if (isset($_GET['u'])){
		$user = true;
	}
	if (is_get_var('token') && is_get_var('ref') && get_get_var('ref') == 'users.php') {
		list($name,$username,$password,$salt,$userProfile,$id,$new_record) = explode('[::]',unserialize(base64_decode($_GET['token'])));
		$user = false;
	}
	if (is_get_var('token') && is_get_var('ref') && get_get_var('ref') == 'deluser.php') {
		list($name,$username,$password,$salt,$userProfile,$id,$record_to_del) = explode('[::]',unserialize(base64_decode($_GET['token'])));
		$user = false;
	}
	if (plx_gu_session_authenticate($name,$username, $password, $remember, $user)) {
		// Redirect to page that referred here - or to the home page
		$redirect = is_get_var('ref') ? urldecode(get_get_var('ref')).(is_get_var('token') ? '?token='.get_get_var('token') : ''): absolute_url('index.php');
		header('Location: '.$redirect);echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$redirect.'">';
		exit;
	}
	else {
		$name = '';
		gu_error(t('Incorrect username or password'));
	}
}elseif (is_get_var('action') && get_get_var('action') == 'login') {
	$username = is_post_var('u') ? get_post_var('u') : '';
	$password = is_post_var('p') ? get_post_var('p') : '';
	$remember = is_post_var('login_remember');	
	 
	if (gu_session_authenticate($username, $password, $remember)) {
		// Redirect to page that referred here - or to the home page
		$redirect = is_get_var('ref') ? urldecode(get_get_var('ref')) : absolute_url('index.php');
		header('Location: '.$redirect);echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$redirect.'">';
		exit;
	}
	else
		gu_error(t('Incorrect username or password'));
}
elseif (is_get_var('action') && get_get_var('action') == 'logout') {
	// Invalidate session flag
	gu_session_set_valid(FALSE);
}

gu_theme_start();

//gu_theme_messages();
?>

<script type="text/javascript">
/* <![CDATA[ */
function loginSubmit(form)
{
	// MD5 encrypt the password and store in hidden field
    form.p.value = hex_md5(form.dummy_p.value);
	
	// Replace the visible password field with Xs
	var mask = 'X';
	for (i = 1; i < form.dummy_p.value.length; ++i)
		mask += 'X';
	form.dummy_p.value = mask;

	return true;
}
/* ]]> */
</script>

<?php
//Body
include_once 'themes/'.gu_config::get('theme_name').'/_login.php';


gu_theme_end();
?>
