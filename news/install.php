<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The install page
 * @maintener : Thomas Ingles
 *
 * Gutama plugin package
 * @version 1.9.0
 * @date	19/05/2018
 * @author Thomas Ingles
*/
define('RPATH',str_replace('install.php','',realpath('install.php')));
include 'inc/gutuma.php';
if (!isset($_SESSION['profil'])){//PluXml
	header('Location:'.PLX_GROOT);
	exit();
}
elseif ($_SESSION['profil'] != PROFIL_ADMIN){//PluXml
	plxMsg::Error($plxMotor->plxPlugins->aPlugins['gutuma']->getLang('L_ERR_UPDATE'));
	header('Location:'.PLX_GROOT.'core/admin/index.php');
	exit();
}

gu_init(FALSE, FALSE);
/**
 * Main install script
 */
function gu_install($collective_name, $admin_username, $admin_password, $admin_email,$salt){
	gu_config::set('collective_name', $collective_name);
	//gu_config::set('admin_username', $admin_username);
	//gu_config::set('admin_password', $admin_password);
	//gu_config::set('admin_email', $admin_email);
	gu_config::set('salt', $salt);
	return gu_config::save();
}
/**
 * Main update script
 */
function gu_update(){
	$lists = gu_list::get_all();//<=1.9.1 to 2.0.0 : get_all($load_addresses = FALSE, $inc_private = TRUE, $tmp = '')
	foreach($lists as $list){//2.0.0 : for all lists
		$tmpFile = realpath(GUTUMA_TEMP_DIR.'/'.$list->get_id().'.i.php');
		if(!file_exists($tmpFile) OR !is_file($tmpFile)){#if unexist
			$list->update('i');//create empty ²opt(in/out) temporary lists (data/gutuma/tmp/##TIME_ID##.i.php)
		}
	}
//2.2.0
	$subscribe_uri = gu_config::get('subscribe_url');
	$subscribe_ori = absolute_url('subscribe.php');
	if($subscribe_ori!=$subscribe_uri){
		global $plxMotor;
		$plxPlugin = $plxMotor->plxPlugins->getInstance('gutuma');
		$plxPlugin->setParam('subscribe_url',$subscribe_uri,'string');//2.2.0
		$plxPlugin->setParam('subscribe_is_good',(int)($subscribe_ori!=$subscribe_uri),'numeric');//2.2.0
		$plxPlugin->saveParams();//2.2.0
	}
	return gu_config::save();
}
if (isset($_profil['salt'])){// If pluxml is used
	$salt = $_profil['salt'];
} else {
	$salt = gu_config::plx_charAleatoire();
}
$update_mode = gu_config::load();// If settings already exist, go into update mode
$install_success = $update_mode && (gu_config::get_version() == GUTUMA_VERSION_NUM);// Check if everything is already up-to-date
if (!$install_success){// Run installtion or update script
	if ($update_mode && is_post_var('update_submit'))
		$install_success = gu_update();
	elseif (!$update_mode && is_post_var('install_submit'))
		$install_success = gu_install(get_post_var('collective_name'), get_post_var('admin_username'), sha1($salt.md5(get_post_var('admin_password'))), get_post_var('admin_email'),$salt);
}
// Get title of page
if ($install_success)
	$title = t('Finished');
elseif ($update_mode)
	$title = t('Update to Gutuma ').GUTUMA_VERSION_NAME;
else
	$title = t('Install Gutuma ').GUTUMA_VERSION_NAME;
gu_theme_start();
echo '</div><div id="content"><h2>'.$title.'</h2>';// Output title
gu_theme_messages();
if ($install_success){
?>
<p class="success"><?php echo t("Congratulations! You can now sending newsletters. Don't forget to check the installation checklist on the home page for any further steps you need to take.");?></p>
<?php if (@unlink('install.php')) { ?>
	<p class="success"><?php echo t('For security reasons, this install script has now been deleted from your server.');?></p>
<?php } else { ?>
	<p class="error"><?php echo t('For security reasons, this install script (<code>install.php</code>) must be deleted from your server.');?></p>
<?php } ?>
<p style="text-align: center"><a href="../../../core/admin/plugin.php?p=gutuma"><?php echo t('Login');?> (PluXml)</a>
<?php if (gu_config::get('theme_name') == 'gutuma'){ ?> - <s><a href="login.php"><?php echo t('Login');?></a></s><?php } echo '</p>';//gu_origin
} elseif ($update_mode) {
?>
<form id="update_form" name="update_form" method="post" action="">
	<div class="menubar">
		<input name="update_submit" type="submit" id="update_submit" value="<?php echo t('Finish');?>" />
	</div>
</form>
<p><?php echo t('There are no major changes required for this update.');?></p>
<p>&nbsp;</p>
<?php
} else {
?>
<form id="install_form" name="install_form" method="post" action="">
	<div class="formfieldset">
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('The following is usually the name of your organization or company. It is used in messages not specific to one list');?></div>
			<div class="formfieldlabel"><?php echo t('Collective name');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('collective_name'); ?></div>
		</div>	
		<div class="formfield" style="display:none;">
			<div class="formfieldlabel"><?php echo t('Administrator username');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_username'); ?></div>
		</div>
		<div class="formfield" style="display:none;">
			<div class="formfieldlabel"><?php echo t('Administrator password');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_password'); ?></div>
		</div>
		<div class="formfield">
			<div class="formfieldcomment"><?php echo t('This address used as the From field of all sent messages. To minimize the chances of your messages being misidentified as spam, ensure that the email address is valid');?></div>
			<div class="formfieldlabel"><?php echo t('Administrator email');?></div>
			<div class="formfieldcontrols"><?php gu_theme_text_control('admin_email'); ?></div>
		</div>
	</div>
	<br/>
	<div class="menubar">
		<input name="install_submit" type="submit" id="install_submit" value="<?php echo t('Install');?>" />
	</div>
</form>
<?php } ?>
<p>&nbsp;</p>
<?php gu_theme_end();