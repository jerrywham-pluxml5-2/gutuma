<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included menu page
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/

$u = gu_config::getUsers();
foreach($u as $k => $v) {
	if ($v['id'] == $_SESSION['user'])
		$u['connect'] = $k;
}
$plxAdmin = plxAdmin::getInstance();
?>

<div id="sidebar">

<ul>
	<li class="nav">
		<a href="<?php echo $plxAdmin->urlRewrite(); ?>" class="homepage" title="<?php echo t('Back to site') ?>"><?php echo t('Back to site');?></a>
		<br/>
		<a href="<?php echo $plxAdmin->urlRewrite();?>core/admin/auth.php?d=1" title="<?php echo t('Quit admin session');?>"  id="logout"><?php echo t('Logout');?></a>
	</li>
	<li class="user">
		<?php echo plxUtils::strCheck($plxAdmin->aUsers[$_SESSION['user']]['name']) ?>

	</li>
	<li class="profil">
		<?php
		if($_SESSION['profil']==PROFIL_ADMIN) printf('%s',L_PROFIL_ADMIN);
		elseif($_SESSION['profil']==PROFIL_MANAGER) printf('%s',L_PROFIL_MANAGER);
		?>

	</li>
	<?php
		$menus = array();

		#menu des fonctionnalités de gutuma
		$menu_gutuma = '';
		if ($_SESSION['profil'] == PROFIL_ADMIN) :
		$menu_gutuma .= '
			<li '. (str_ends($_SERVER['SCRIPT_NAME'], '/index.php') ? 'class="menu active sub">' : 'class="menu sub">').'<a href="index.php">'.t('Home').'</a></li>
			';
		endif;
		$menu_gutuma .= '
			<li '. (str_ends($_SERVER['SCRIPT_NAME'], '/compose.php') || str_ends($_SERVER['SCRIPT_NAME'], '/newsletters.php') ? 'class="menu active sub">' : 'class="menu sub">') .' <a href="compose.php">'.t('Newsletters').'</a></li>
			<li '. (str_ends($_SERVER['SCRIPT_NAME'], '/lists.php') || str_ends($_SERVER['SCRIPT_NAME'], '/editlist.php') ? 'class="menu active sub">' : 'class="menu sub">') .'<a href="lists.php">'. t('Lists').'</a></li>
			';
		if($_SESSION['profil'] == PROFIL_ADMIN):
		$menu_gutuma .= '
			<li '. (str_ends($_SERVER['SCRIPT_NAME'], '/integrate.php') ? 'class="menu active sub">' : 'class="menu sub">') .'<a href="integrate.php">'. t('Gadgets').'</a></li>
			<li '. (str_ends($_SERVER['SCRIPT_NAME'], '/settings.php') ? 'class="menu active sub">' : 'class="menu sub">') .'<a href="settings.php">'. t('Settings').'</a></li>'.PHP_EOL;
		endif;
		$menu_gutuma .= '
			<li class="sub">
				<img id="imghead" title="'.gu_config::get('application_name').t(' Powered by').' Pluxml '.PLX_VERSION.' &amp; '.t('Gutuma').' '.GUTUMA_VERSION_NAME.'" src="themes/'.gu_config::get('theme_name').'/images/gutuma.png" />
				<sup id="sitename"><sub><img style="float: right;" title="'.t('Gutuma').' for Pluxml" src="themes/'.gu_config::get('theme_name').'/favicon.png" /><i>'.gu_config::get('application_name').' '.t('Powered by Gutuma').'</i></sub></sup>
			</li>'.PHP_EOL;

		$userId = ($_SESSION['profil'] < PROFIL_WRITER ? '[0-9]{3}' : $_SESSION['user']);
		$nbartsmod = $plxAdmin->nbArticles('all', $userId, '_');
		$arts_mod = $nbartsmod>0 ? '&nbsp;<a class="cpt" href="'.$plxAdmin->urlRewrite().'core/admin/index.php?sel=mod&amp;page=1" title="'.L_ALL_AWAITING_MODERATION.'">'.$nbartsmod.'</a>':'';
		$menus[] = str_replace('active','',plxUtils::formatMenu(L_MENU_ARTICLES, $plxAdmin->urlRewrite().'core/admin/index.php?page=1', L_MENU_ARTICLES_TITLE, false, false,$arts_mod));

		if(isset($_GET['a'])) # edition article
			$menus[] = plxUtils::formatMenu(L_MENU_NEW_ARTICLES_TITLE, $plxAdmin->urlRewrite().'core/admin/article.php', L_MENU_NEW_ARTICLES, false, false, '', false);
		else # nouvel article
			$menus[] = plxUtils::formatMenu(L_MENU_NEW_ARTICLES_TITLE, $plxAdmin->urlRewrite().'core/admin/article.php', L_MENU_NEW_ARTICLES);

		$menus[] = plxUtils::formatMenu(L_MENU_MEDIAS, $plxAdmin->urlRewrite().'core/admin/medias.php', L_MENU_MEDIAS_TITLE);

		if($_SESSION['profil'] <= PROFIL_MANAGER) {
			$menus[] = plxUtils::formatMenu(L_MENU_STATICS, $plxAdmin->urlRewrite().'core/admin/statiques.php', L_MENU_STATICS_TITLE);
		}
		if($_SESSION['profil'] <= PROFIL_MODERATOR) {
			$nbcoms = $plxAdmin->nbComments('offline');
			$coms_offline = $nbcoms>0 ? '&nbsp;<a class="cpt" href="../comments.php?sel=offline&amp;page=1">'.$plxAdmin->nbComments('offline').'</a>':'';
			$menus[] = plxUtils::formatMenu(L_MENU_COMMENTS, $plxAdmin->urlRewrite().'core/admin/comments.php?page=1', L_MENU_COMMENTS_TITLE, false, false, $coms_offline);
		}
		if($_SESSION['profil'] <= PROFIL_EDITOR) {
			$menus[] = plxUtils::formatMenu(L_MENU_CATEGORIES,$plxAdmin->urlRewrite().'core/admin/categories.php', L_MENU_CATEGORIES_TITLE);
		}
		if($_SESSION['profil'] == PROFIL_ADMIN) {
			$menus[] = plxUtils::formatMenu(L_MENU_CONFIG, $plxAdmin->urlRewrite().'core/admin/parametres_base.php', L_MENU_CONFIG_TITLE, false, false, '', false);

			if (preg_match('/parametres/',basename($_SERVER['SCRIPT_NAME']))) {
				$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_BASE,$plxAdmin->urlRewrite().'core/admin/parametres_base.php', L_MENU_CONFIG_BASE_TITLE, 'menu');
				$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_VIEW,$plxAdmin->urlRewrite().'core/admin/parametres_affichage.php', L_MENU_CONFIG_VIEW_TITLE, 'menu');
				$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_USERS,$plxAdmin->urlRewrite().'core/admin/parametres_users.php', L_MENU_CONFIG_USERS_TITLE, 'menu');
				$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_ADVANCED,$plxAdmin->urlRewrite().'core/admin/parametres_avances.php', L_MENU_CONFIG_ADVANCED_TITLE, 'menu');
				$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_PLUGINS,$plxAdmin->urlRewrite().'core/admin/parametres_plugins.php', L_MENU_CONFIG_PLUGINS_TITLE, 'menu');
				$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_INFOS,$plxAdmin->urlRewrite().'core/admin/parametres_infos.php', L_MENU_CONFIG_INFOS_TITLE, 'menu');
			}
		}
		$menus[] = plxUtils::formatMenu(L_MENU_PROFIL, $plxAdmin->urlRewrite().'core/admin/profil.php', L_MENU_PROFIL_TITLE);

		# récuperation des menus admin pour les plugins
		foreach($plxAdmin->plxPlugins->aPlugins as $plugName => $plugInstance) {
			if($plugName == 'gutuma') continue;
			if($plugInstance AND is_file(PLX_PLUGINS.$plugName.'/admin.php')) {
				if($plxAdmin->checkProfil($plugInstance->getAdminProfil(),false)) {
					if($plugInstance->adminMenu) {
						$menu = plxUtils::formatMenu(plxUtils::strCheck($plugInstance->adminMenu['title']), $plxAdmin->racine.'core/admin/plugin.php?p='.$plugName, plxUtils::strCheck($plugInstance->adminMenu['caption']));
						if($plugInstance->adminMenu['position']!='')//Fix : duplicated menu if position  PluXml.5.4+
							array_splice($menus, ($plugInstance->adminMenu['position']-1), 0, $menu.($plugName == 'gutuma'?$menu_gutuma:''));
						else
							$menus[] = $menu.($plugName == 'gutuma'?$menu_gutuma:'');
					} else {
						if ($plugName == 'gutuma') {
							$menus[] = '<li class="menu"><a href="'.$plxAdmin->racine.'core/admin/plugin.php?p='.$plugName.'" title="'.t('Gutuma version:').' '.GUTUMA_VERSION_NAME.'">'.$plugInstance->getLang('L_ADMIN_MENU_NAME').'</a></li>'.$menu_gutuma;
						}else {
							$menus[] = plxUtils::formatMenu(plxUtils::strCheck($plugInstance->getInfo('title')), $plxAdmin->racine.'core/admin/plugin.php?p='.$plugName, plxUtils::strCheck($plugInstance->getInfo('title')));
						}
					}
				}
			}
		}

		#gutuma on top
		array_splice($menus, 0, 0, $menu_gutuma);

		# Hook Plugins
		eval($plxAdmin->plxPlugins->callHook('AdminTopMenus'));

		echo implode('', $menus);
?>

	<li class="pluxml">
		<a class="version" title="<?php echo gu_config::get('application_name').t(' Powered by') .' ' . t('Gutuma') ?>" href="<?php echo GUTUMA_URL ?>"><?php echo t('Gutuma').'&nbsp;'.GUTUMA_VERSION_NAME ?></a> &amp;
		<a title="PluXml" href="http://www.pluxml.org">Pluxml&nbsp;<?php echo $plxAdmin->aConf['version'] ?></a>
		<br/>
<!--
		<a href="<?php echo GUTUMA_URL; ?>" onclick="window.open(this.href);return false;">Gutuma</a> <?php echo t('is released under the GPL');?> | &copy; Rowan
-->
	</li>
</ul>

</div><!-- sidebar -->
