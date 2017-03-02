<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included settings page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

include_once '_menu.php';?>
<h1><?php echo t('Add a new user');?></h1>

<?php gu_theme_messages();?>

<form id="edit_form" name="edit_form" method="post" action="<?php echo absolute_url('users.php');?>?token=<?php echo $_GET['token'];?>">
	<div class="formfieldset">

		<div class="formfield">
			<div class="formfieldlabel"><?php echo t('Name');?></div>
			<div class="formfieldcontrols"><input name="name" type="text" class="textfield" id="name" value="<?php echo $user_name ;?>" readonly="readonly"  /></div>
		</div>
		<div class="formfield">
			<div class="formfieldlabel"><?php echo t('Login');?></div>
			<div class="formfieldcontrols"><input name="login" type="text" class="textfield" id="login" value="<?php echo $user_login ;?>" readonly="readonly"  /></div>
			<div class="formfielddivider"></div>
			<div class="formfieldlabel"><?php echo t('Profile');?></div>
			<div class="formfieldcontrols"><input name="userProfile" type="text" class="textfield" id="userProfile" value="<?php echo $user_userProfile ;?>" readonly="readonly"  /></div>
			<div class="formfielddivider"></div>
			<div class="formfieldcontrols"><input name="password" type="hidden" class="textfield" id="password" value="<?php echo $user_password ;?>" readonly="readonly"  /></div>
			<input name="admin_password" type="hidden" id="admin_password" value="<?php echo $password ;?>" readonly="readonly"  />
			<input name="admin_name" type="hidden" id="admin_name" value="<?php echo $username ;?>" readonly="readonly"  />
			<input name="salt" type="hidden" id="salt" value="<?php echo base64_encode($user_salt) ;?>" readonly="readonly"  />
			<input name="id" type="hidden" id="id" value="<?php echo $user_id ;?>" readonly="readonly"  />
		</div>
		<br/>	
		<?php if (isset($ok)) :unset($ok);?>
		
			<input name="" type="submit" id="save_settings" value="<?php echo t('Back');?>" />
		<?php else :?>
		
			<input name="save_settings" type="submit" id="save_settings" value="<?php echo t('Save');?>" />
		<?php endif;?>
		
	</div>
</form>
<p>&nbsp;</p>