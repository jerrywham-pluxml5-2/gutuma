<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included menu page
 * @modifications Thomas Ingles
 */
/* Gutama plugin package
 * @version 1.9
 * @date	08/06/2017
 * @author	Cyril MAGUIRE
*/

$u = gu_config::getUsers();
foreach($u as $k => $v) {
	if ($v['id'] == $_SESSION['user'])
		$u['connect'] = $k;
}
?>
	<aside class="aside col sml-12 med-3 lrg-2 sml-text-left med-text-right">
		<header class="header sml-text-center med-text-right">
			<ul class="unstyled-list head">
				<li>
					<small><a class="back-site" href="<?php echo $plxAdmin->urlRewrite(); ?>" title="<?php echo L_BACK_TO_SITE_TITLE ?>"><?php echo L_BACK_TO_SITE;?></a></small>
				</li>
				<li>
<?php if(isset($plxAdmin->aConf['homestatic']) AND !empty($plxAdmin->aConf['homestatic'])) : ?>
					<small><a class="back-blog" href="<?php echo $plxAdmin->urlRewrite('?blog'); ?>" title="<?php echo L_BACK_TO_BLOG_TITLE ?>"><?php echo L_BACK_TO_BLOG;?></a></small>
<?php else: ?>&nbsp;
<?php endif; ?>
				</li>
				<li>
					<small><a class="logout" href="<?php echo $plxAdmin->urlRewrite(); ?>core/admin/auth.php?d=1" title="<?php echo L_ADMIN_LOGOUT_TITLE ?>"><?php echo L_ADMIN_LOGOUT ?></a></small>
				</li>
			</ul>
			<ul class="unstyled-list profil">
				<li>
					<h1 class="h5 no-margin site-name"><strong><?php echo plxUtils::strCheck($plxAdmin->aConf['title']) ?></strong></h1>
				</li>
				<li>
					<strong><?php echo plxUtils::strCheck($plxAdmin->aUsers[$_SESSION['user']]['name']) ?></strong>&nbsp;:
					<em>
						<?php if($_SESSION['profil']==PROFIL_ADMIN) echo L_PROFIL_ADMIN;
						elseif($_SESSION['profil']==PROFIL_MANAGER) echo L_PROFIL_MANAGER;
						elseif($_SESSION['profil']==PROFIL_MODERATOR) echo L_PROFIL_MODERATOR;
						elseif($_SESSION['profil']==PROFIL_EDITOR) echo L_PROFIL_EDITOR;
						else echo L_PROFIL_WRITER; ?>
					</em>
				</li>
				<li><small><a class="version" title="<?php echo gu_config::get('application_name').t(' Powered by') .' ' . t('Gutuma') ?>" href="<?php echo GUTUMA_URL ?>"><?php echo t('Gutuma').'&nbsp;'.GUTUMA_VERSION_NAME ?></a> &amp; <a class="version" title="PluXml" href="http://www.pluxml.org">PluXml&nbsp;<?php echo $plxAdmin->aConf['version'] ?></a></small></li>
			</ul>
		</header>
		<nav class="responsive-menu">
			<label for="nav"><?php echo L_MENU ?></label>
			<input type="checkbox" id="nav" />
			<ul id="responsive-menu" class="menu vertical expanded">
				<?php
		#menu des fonctionnalités de gutuma
		$menu_gutuma = '';
		if($_SESSION['profil'] == PROFIL_ADMIN):
		$menu_gutuma .= '
			<li class="menu'.(str_ends($_SERVER['SCRIPT_NAME'], '/index.php') ? ' active ' : ' ').'menu-config"><a href="index.php">'.t('Home').'</a></li>
			';
		endif;
		$menu_gutuma .= '
			<li class="menu'.(str_ends($_SERVER['SCRIPT_NAME'], '/compose.php') || str_ends($_SERVER['SCRIPT_NAME'], '/newsletters.php') ? ' active ' : ' ').'menu-config"><a href="compose.php">'.t('Newsletters').'</a></li>
			<li class="menu'.(str_ends($_SERVER['SCRIPT_NAME'], '/lists.php') || str_ends($_SERVER['SCRIPT_NAME'], '/editlist.php') ? ' active ' : ' ').'menu-config"><a href="lists.php">'. t('Lists').'</a></li>'.PHP_EOL;
		if ($_SESSION['profil'] == PROFIL_ADMIN) :
		$menu_gutuma .= '
			<li class="menu'.(str_ends($_SERVER['SCRIPT_NAME'], '/integrate.php') ? ' active ' : ' ').'menu-config"><a href="integrate.php">'. t('Gadgets').'</a></li>
			<li class="menu'.(str_ends($_SERVER['SCRIPT_NAME'], '/settings.php') ? ' active ' : ' ').'menu-config"><a href="settings.php">'. t('Settings').'</a></li>'.PHP_EOL;
		endif;
		$menu_gutuma .= '
			<li class="menu active menu-config">
				<img id="imghead" title="'.gu_config::get('application_name').t(' Powered by').' Pluxml '.PLX_VERSION.' &amp; '.t('Gutuma').' '.GUTUMA_VERSION_NAME.'" src="themes/'.gu_config::get('theme_name').'/images/gutuma.png" />
				<sup id="sitename"><sub><img style="float: right;" title="'.t('Gutuma').' for Pluxml" src="themes/'.gu_config::get('theme_name').'/favicon.png" /><i>'.gu_config::get('application_name').' '.t('Powered by Gutuma').'</i></sub></sup>
			</li>'.PHP_EOL;

					$menus = array();
					$userId = ($_SESSION['profil'] < PROFIL_WRITER ? '[0-9]{3}' : $_SESSION['user']);
					$nbartsmod = $plxAdmin->nbArticles('all', $userId, '_');
					$arts_mod = $nbartsmod>0 ? '<span class="badge" onclick="window.location=\''.$plxAdmin->urlRewrite().'core/admin/index.php?sel=mod&amp;page=1\';return false;">'.$nbartsmod.'</span>':'';
					$menus[] = plxUtils::formatMenu(L_MENU_ARTICLES, $plxAdmin->urlRewrite().'core/admin/index.php?page=1', L_MENU_ARTICLES_TITLE, false, false,$arts_mod,'',false);// Fix articles : no highlight Plux/index.php menu is active (white) when admin consult info page (news/index.php) in gutuma menu

					if(isset($_GET['a'])) # edition article
						$menus[] = plxUtils::formatMenu(L_MENU_NEW_ARTICLES_TITLE, $plxAdmin->urlRewrite().'core/admin/article.php', L_MENU_NEW_ARTICLES, false, false, '', false);
					else # nouvel article
						$menus[] = plxUtils::formatMenu(L_MENU_NEW_ARTICLES_TITLE, $plxAdmin->urlRewrite().'core/admin/article.php', L_MENU_NEW_ARTICLES);

					$menus[] = plxUtils::formatMenu(L_MENU_MEDIAS, $plxAdmin->urlRewrite().'core/admin/medias.php', L_MENU_MEDIAS_TITLE);

					if($_SESSION['profil'] <= PROFIL_MANAGER)
						$menus[] = plxUtils::formatMenu(L_MENU_STATICS, $plxAdmin->urlRewrite().'core/admin/statiques.php', L_MENU_STATICS_TITLE);

					if($_SESSION['profil'] <= PROFIL_MODERATOR) {
						$nbcoms = $plxAdmin->nbComments('offline');
						$coms_offline = $nbcoms>0 ? '<span class="badge" onclick="window.location=\''.$plxAdmin->urlRewrite().'core/admin/comments.php?sel=offline&amp;page=1\';return false;">'.$plxAdmin->nbComments('offline').'</span>':'';
						$menus[] = plxUtils::formatMenu(L_MENU_COMMENTS, $plxAdmin->urlRewrite().'core/admin/comments.php?page=1', L_MENU_COMMENTS_TITLE, false, false, $coms_offline);
					}

					if($_SESSION['profil'] <= PROFIL_EDITOR)
						$menus[] = plxUtils::formatMenu(L_MENU_CATEGORIES, $plxAdmin->urlRewrite().'core/admin/categories.php', L_MENU_CATEGORIES_TITLE);

					$menus[] = plxUtils::formatMenu(L_MENU_PROFIL, $plxAdmin->urlRewrite().'core/admin/profil.php', L_MENU_PROFIL_TITLE);

					if($_SESSION['profil'] == PROFIL_ADMIN) {
						$menus[] = plxUtils::formatMenu(L_MENU_CONFIG, $plxAdmin->urlRewrite().'core/admin/parametres_base.php', L_MENU_CONFIG_TITLE, false, false, '', false);
						if (preg_match('/parametres/',basename($_SERVER['SCRIPT_NAME']))) {
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_BASE, $plxAdmin->urlRewrite().'core/admin/parametres_base.php', L_MENU_CONFIG_BASE_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_VIEW, $plxAdmin->urlRewrite().'core/admin/parametres_affichage.php', L_MENU_CONFIG_VIEW_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_USERS, $plxAdmin->urlRewrite().'core/admin/parametres_users.php', L_MENU_CONFIG_USERS_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_ADVANCED, $plxAdmin->urlRewrite().'core/admin/parametres_avances.php', L_MENU_CONFIG_ADVANCED_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_THEMES, $plxAdmin->urlRewrite().'core/admin/parametres_themes.php', L_THEMES_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_PLUGINS, $plxAdmin->urlRewrite().'core/admin/parametres_plugins.php', L_MENU_CONFIG_PLUGINS_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_INFOS, $plxAdmin->urlRewrite().'core/admin/parametres_infos.php', L_MENU_CONFIG_INFOS_TITLE, 'menu-config');
						}
					}

					# récuperation des menus admin pour les plugins
					foreach($plxAdmin->plxPlugins->aPlugins as $plugName => $plugInstance) {
						if($plugName == 'gutuma') continue;
						if($plugInstance AND is_file(PLX_PLUGINS.$plugName.'/admin.php')) {
							if($plxAdmin->checkProfil($plugInstance->getAdminProfil(),false)) {
								if($plugInstance->adminMenu) {
									$menu = plxUtils::formatMenu(plxUtils::strCheck($plugInstance->adminMenu['title']), $plxAdmin->urlRewrite().'core/admin/plugin.php?p='.$plugName, plxUtils::strCheck($plugInstance->adminMenu['caption']));
									if($plugInstance->adminMenu['position']!='')
										array_splice($menus, ($plugInstance->adminMenu['position']-1), 0, $menu.($plugName == 'gutuma'?$menu_gutuma:''));
									else
										$menus[] = $menu.($plugName == 'gutuma'?$menu_gutuma:'');
								} else {
									if ($plugName == 'gutuma')
										$menus[] = '<li id="mnu_gutuma" class="menu in_gutuma"><a href="'.$plxAdmin->racine.'core/admin/plugin.php?p='.$plugName.'" title="'.t('Gutuma version:').' '.GUTUMA_VERSION_NAME.'">'.$plugInstance->getLang('L_ADMIN_MENU_NAME').'</a></li>'.$menu_gutuma;
									else
										$menus[] = plxUtils::formatMenu(plxUtils::strCheck($plugInstance->getInfo('title')), $plxAdmin->urlRewrite().'core/admin/plugin.php?p='.$plugName, plxUtils::strCheck($plugInstance->getInfo('title')));
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
			</ul>
		</nav>
	</aside>