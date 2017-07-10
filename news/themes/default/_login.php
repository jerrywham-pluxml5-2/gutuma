<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included login page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

gu_theme_messages();

?>

<p>&nbsp;</p>
<div style="text-align: center">
	<form name="login_form" method="post" action="login.php?action=login<?php echo (is_get_var('ref') ? ('&amp;ref='.urlencode(get_get_var('ref'))) : ''); ?>" onsubmit="return loginSubmit(this);">
		<p><?php echo t('Username');?><br/><input name="u" type="text" class="textfield" id="u" value="<?php echo @$name;?>" /><br/>
		<?php echo t('Password');?><br/><input type="hidden" id="p" name="p" /><input name="dummy_p" type="password" class="textfield" id="dummy_p" />
		</p>
		<p>
		  <input name="login_remember" type="checkbox" id="login_remember" value="TRUE" />
<?php echo t('Remember me');?></p>  
		<p><input name="login_submit" type="submit" id="login_submit" value="<?php echo t('Log In');?>" />
		</p>
	</form>
</div>
<p>&nbsp;</p>