<?php if(!defined('PLX_ROOT')) exit;
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included header page : Mix of pluxml/core/admin/theme/top.php v5.6
 * @maintainer : Thomas Ingles
 *
 * Gutama plugin package
 * @version 2.2.1
 * @date	26/05/2020
 * @author	Cyril MAGUIRE, Thomas Ingles
*/
$plxAdmin = @plxAdmin::getInstance();
if(!defined('L_BACK_TO_SITE_TITLE') ) {#if lang 2004 (in dev at this moment, maybe next release 5.8.4)
  define('L_BACK_TO_SITE_TITLE', L_BACK_HOMEPAGE_TITLE);#
 if(!defined('L_BACK_TO_SITE'))
  define('L_BACK_TO_SITE', L_HOMEPAGE);#
 if(!defined('L_MENU_NEW_ARTICLES_TITLE'))
  define('L_MENU_NEW_ARTICLES_TITLE', L_NEW_ARTICLE);
 if(!defined('L_MENU_NEW_ARTICLES'))
  define('L_MENU_NEW_ARTICLES', L_NEW_ARTICLE);#_NEW
 if(!defined('L_MENU_COMMENTS'))
  define('L_MENU_COMMENTS', L_COMMENTS);
 if(!defined('L_MENU_CATEGORIES'))
  define('L_MENU_CATEGORIES', L_CATEGORIES);
 if(!defined('L_MENU_PROFIL'))
  define('L_MENU_PROFIL', L_PROFIL);
}
#multipes PluXml versions compatibility (5.4 ~ 6.0) THEME
define('GLX_URL_REPO', defined('PLX_URL_REPO')?PLX_URL_REPO:'https://www.pluxml.org');
if(isset($_GET["del"]) AND $_GET["del"]=="install") {
	if(@unlink(PLX_ROOT.'install.php'))
		plxMsg::Info(L_DELETE_SUCCESSFUL);
	else
		plxMsg::Error(L_DELETE_FILE_ERR.' install.php');
	header("Location: index.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $plxAdmin->aConf['default_lang'] ?>">
<head>
	<meta name="robots" content="noindex, nofollow" />
	<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
	<title><?php echo GUTUMA_TITLE; ?> - <?php echo plxUtils::strCheck($plxAdmin->aConf['title']) ?> - <?php echo L_ADMIN ?></title>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo strtolower(GUTUMA_ENCODING) ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo PLX_MORE ?>admin/theme/plucss<?php echo version_compare(@PLX_VERSION,'5.7','>')?'.min':'' ?>.css?ver=<?php echo PLX_VERSION ?>" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo PLX_MORE ?>admin/theme/theme.css?ver=<?php echo PLX_VERSION ?>" media="screen" />
	<link rel="stylesheet" type="text/css" href="themes/<?php echo gu_config::get('theme_name');?>/css/style.css?v=<?php echo GUTUMA_VERSION_NAME ?>" media="screen" />
<?php if(version_compare(@PLX_VERSION,'5.7','>')){ $pathfont = (version_compare(@PLX_VERSION,'5.8.3','>')? 'fontello/css': 'fonts'); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo PLX_MORE ?>admin/theme/<?=$pathfont?>/fontello.css?v=<?php echo PLX_VERSION ?>" media="screen" />
<?php } ?>
	<link rel="icon" href="<?php echo PLX_MORE ?>admin/theme/images/favicon.png" />
<?php
	if(!empty ($custom_admincss_file = PLX_GROOT.$plxAdmin->aConf['custom_admincss_file']) && is_file($custom_admincss_file)) echo '	<link rel="stylesheet" type="text/css" href="'.$custom_admincss_file.'?v='.filemtime($custom_admincss_file).'" media="screen" />'.PHP_EOL;
	if(gu_session_is_valid()){#only valid user
		if(!empty ($admincss_file = PLX_GROOT.$plxAdmin->aConf['racine_plugins'].'admin.css') && is_file($admincss_file)) echo '	<link rel="stylesheet" type="text/css" href="'.$admincss_file.'?v='.filemtime($admincss_file).'" media="screen" />'.PHP_EOL;
?>
<!--
	<script src="<?php echo PLX_MORE ?>lib/functions.js?ver=<?php echo PLX_VERSION ?>"></script>
	<script src="<?php echo PLX_MORE ?>lib/visual.js?ver=<?php echo PLX_VERSION ?>"></script>
	<script src="<?php echo PLX_MORE ?>lib/mediasManager.js?ver=<?php echo PLX_VERSION ?>"></script>
	<script defer src="<?php echo PLX_MORE ?>lib/multifiles.js?ver=<?php echo PLX_VERSION ?>"></script>
-->
<?php
		# Hook Plugins
		eval($plxAdmin->plxPlugins->callHook('AdminTopEndHead'));
	}# fi only valid user
?>
	<script type="text/javascript" src="js/misc.min.js?v=<?php echo GUTUMA_VERSION_NAME ?>"></script>
	<script type="text/javascript" src="js/tw-sack.min.js?v=<?php echo GUTUMA_VERSION_NAME ?>"></script>
	<script type="text/javascript" src="js/md5.min.js?v=<?php echo GUTUMA_VERSION_NAME ?>"></script>
	<script type="text/javascript" src="js/sha1.min.js?v=<?php echo GUTUMA_VERSION_NAME ?>"></script>
</head>

<body id="<?php echo basename($_SERVER['SCRIPT_NAME'], ".php") ?>"<?php echo $nomenu?' class="subscribe"':'';?>>

<main class="main grid">
<?php if (gu_is_demo()) { ?>
	<div id="demobanner">DEMO MODE</div>
<?php }
if (!$nomenu) {
	if (gu_session_is_valid()) {
		include ('_menu.php');
	} else {# !gu_session_is_valid ?>
	<aside class="aside col sml-12 med-3 lrg-2 sml-text-left med-text-right">
		<header class="header sml-text-center med-text-right">
			<ul class="unstyled-list head">
				<li>
					<small><a class="back-site" href="<?php echo PLX_GROOT ?>" title="<?php echo L_BACK_TO_SITE_TITLE ?>"><?php echo L_BACK_TO_SITE;?></a></small>
				</li>
				<li>
<?php if(isset($plxAdmin->aConf['homestatic']) AND !empty($plxAdmin->aConf['homestatic'])) : ?>
					<small><a class="back-blog" href="<?php echo $plxAdmin->urlRewrite('?blog'); ?>" title="<?php echo L_BACK_TO_BLOG_TITLE ?>"><?php echo L_BACK_TO_BLOG;?></a></small>
<?php else: ?>&nbsp;
<?php endif; ?>
				</li>
				<li>
					<small><a class="logout" href="<?php echo PLX_MORE ?>admin/auth.php?d=1" title="<?php echo L_ADMIN_LOGOUT_TITLE ?>"><?php echo L_ADMIN_LOGOUT ?></a></small>
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
				<li><small><a class="version" title="<?php echo gu_config::get('application_name').t(' Powered by') .' ' . t('Gutuma') ?>" href="<?php echo GUTUMA_URL ?>"><?php echo t('Gutuma').'&nbsp;'.GUTUMA_VERSION_NAME ?></a> &amp; <a class="version" title="PluXml" href="<?php echo defined('PLX_REPO_URL')?PLX_REPO_URL:GLX_URL_REPO ?>">PluXml&nbsp;<?php echo $plxAdmin->aConf['version'] ?></a></small></li>
			</ul>
		</header>
		<nav class="responsive-menu">
			<label for="nav"><?php echo L_MENU ?></label>
			<input type="checkbox" id="nav" />
			<ul id="responsive-menu" class="menu vertical expanded">
				<?php
					$menus = array();
					$userId = ($_SESSION['profil'] < PROFIL_WRITER ? '[0-9]{3}' : $_SESSION['user']);
					$nbartsmod = $plxAdmin->nbArticles('all', $userId, '_');
					$arts_mod = $nbartsmod>0 ? '<span class="badge" onclick="window.location=\''.PLX_MORE.'admin/index.php?sel=mod&amp;page=1\';return false;">'.$nbartsmod.'</span>':'';
					$menus[] = plxUtils::formatMenu(L_MENU_ARTICLES, PLX_MORE.'admin/index.php?page=1', L_MENU_ARTICLES_TITLE, false, false,$arts_mod);

					if(isset($_GET['a'])) # edition article
						$menus[] = plxUtils::formatMenu(L_MENU_NEW_ARTICLES_TITLE, PLX_MORE.'admin/article.php', L_MENU_NEW_ARTICLES, false, false, '', false);
					else # nouvel article
						$menus[] = plxUtils::formatMenu(L_MENU_NEW_ARTICLES_TITLE, PLX_MORE.'admin/article.php', L_MENU_NEW_ARTICLES);

					$menus[] = plxUtils::formatMenu(L_MENU_MEDIAS, PLX_MORE.'admin/medias.php', L_MENU_MEDIAS_TITLE);

					if($_SESSION['profil'] <= PROFIL_MANAGER)
						$menus[] = plxUtils::formatMenu(L_MENU_STATICS, PLX_MORE.'admin/statiques.php', L_MENU_STATICS_TITLE);

					if($_SESSION['profil'] <= PROFIL_MODERATOR) {
						$nbcoms = $plxAdmin->nbComments('offline');
						$coms_offline = $nbcoms>0 ? '<span class="badge" onclick="window.location=\''.PLX_MORE.'admin/comments.php?sel=offline&amp;page=1\';return false;">'.$plxAdmin->nbComments('offline').'</span>':'';
						$menus[] = plxUtils::formatMenu(L_MENU_COMMENTS, PLX_MORE.'admin/comments.php?page=1', L_MENU_COMMENTS_TITLE, false, false, $coms_offline);
					}

					if($_SESSION['profil'] <= PROFIL_EDITOR)
						$menus[] = plxUtils::formatMenu(L_MENU_CATEGORIES, PLX_MORE.'admin/categories.php', L_MENU_CATEGORIES_TITLE);

					$menus[] = plxUtils::formatMenu(L_MENU_PROFIL, PLX_MORE.'admin/profil.php', L_MENU_PROFIL_TITLE);

					if($_SESSION['profil'] == PROFIL_ADMIN) {
						$menus[] = plxUtils::formatMenu(L_MENU_CONFIG, PLX_MORE.'admin/parametres_base.php', L_MENU_CONFIG_TITLE, false, false, '', false);
						if (preg_match('/parametres/',basename($_SERVER['SCRIPT_NAME']))) {
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_BASE, PLX_MORE.'admin/parametres_base.php', L_MENU_CONFIG_BASE_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_VIEW, PLX_MORE.'admin/parametres_affichage.php', L_MENU_CONFIG_VIEW_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_USERS, PLX_MORE.'admin/parametres_users.php', L_MENU_CONFIG_USERS_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_ADVANCED, PLX_MORE.'admin/parametres_avances.php', L_MENU_CONFIG_ADVANCED_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_THEMES, PLX_MORE.'admin/parametres_themes.php', L_THEMES_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_PLUGINS, PLX_MORE.'admin/parametres_plugins.php', L_MENU_CONFIG_PLUGINS_TITLE, 'menu-config');
							$menus[] = plxUtils::formatMenu(L_MENU_CONFIG_INFOS, PLX_MORE.'admin/parametres_infos.php', L_MENU_CONFIG_INFOS_TITLE, 'menu-config');
						}
					}

					# récuperation des menus admin pour les plugins
					foreach($plxAdmin->plxPlugins->aPlugins as $plugName => $plugInstance) {
						if($plugInstance AND is_file(PLX_PLUGINS.$plugName.'/admin.php')) {
							if($plxAdmin->checkProfil($plugInstance->getAdminProfil(),false)) {
								if($plugInstance->adminMenu) {
									$menu = plxUtils::formatMenu(plxUtils::strCheck($plugInstance->adminMenu['title']), PLX_MORE.'admin/plugin.php?p='.$plugName, plxUtils::strCheck($plugInstance->adminMenu['caption']));
									if($plugInstance->adminMenu['position']!='')
										array_splice($menus, ($plugInstance->adminMenu['position']-1), 0, $menu);
									else
										$menus[] = $menu;
								} else {
									$menus[] = plxUtils::formatMenu(plxUtils::strCheck($plugInstance->getInfo('title')), PLX_MORE.'admin/plugin.php?p='.$plugName, plxUtils::strCheck($plugInstance->getInfo('title')));
								}
							}
						}
					}

					# Hook Plugins
					eval($plxAdmin->plxPlugins->callHook('AdminTopMenus'));
					echo implode('', $menus);
				?>
			</ul>
		</nav>
	</aside>
<?php } ?>
	<section class="section col sml-12 med-9 med-offset-3 lrg-10 lrg-offset-2">
<?php } else {# nomenu ?>
	<!-- <aside class="aside col sml-12 med-3 lrg-2 sml-text-left med-text-right hide"></aside> -->
	<section class="col sml-12">
<?php } ?>
		<noscript><h3 class="warning">Oups, No JS</h3></noscript>
<?php
		if(is_file($plxAdmin->urlRewrite().'install.php')) echo '<p class="alert red">'.L_WARNING_INSTALLATION_FILE.'</p>';
		//plxMsg::Display();
#hook of pluXml (admin) integration (adhesion hook notice & warning) #tep #NOT IN StandAlone
if(!$nomenu)
	eval($plxAdmin->plxPlugins->callHook('AdminTopBottom')) ?>
