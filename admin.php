<?php
/**
 * Gestion des utilisateurs pour le module de newsletters
 *
 * @version 1.6
 * @date	01/10/2013
 * @package plugin Gutuma
 * @author	Cyril MAGUIRE
 **/


# Control du token du formulaire
plxToken::validateFormToken($_POST);

# Controle de l'accès à la page en fonction du profil de l'utilisateur connecté
$plxAdmin->checkProfil(PROFIL_ADMIN,PROFIL_MANAGER);

# Tableau des profils
$aProfils = array(
	PROFIL_ADMIN => L_PROFIL_ADMIN,
	PROFIL_MANAGER => L_PROFIL_MANAGER,
	PROFIL_MODERATOR => L_PROFIL_MODERATOR,
	PROFIL_EDITOR => L_PROFIL_EDITOR,
	PROFIL_WRITER => L_PROFIL_WRITER
);

$ok_config = FALSE;
if (file_exists($plxPlugin->listsDir.'/inc/config.php')) {
	//Le fichier de config existe donc le module a été installé
	$ok_config = TRUE;
	// Récupération de la config de Gutuma
	// Version encodée
	eval(base64_decode(substr(file_get_contents($plxPlugin->listsDir.'/inc/config.php'),9,-5)));
	// Version décodée
	//eval(substr(file_get_contents($plxPlugin->listsDir.'/inc/config.php'),7,-4));
}
if(!empty($_POST)) {
	foreach ($_POST['user'] as $key => $value) {
		$plxPlugin->setParam('user_'.$key, $value, 'cdata');
		$_userid = $key;

	}	
	$plxPlugin->saveParams();
	$Gut_users = unserialize($gu_config['users']);
	foreach($_POST['user'] as $id => $activation){
		if ($activation == 'activé') {
			$Gut_users[$plxAdmin->aUsers[$id]['login']] = array(
				'id'=>$id,
				'login'=>$plxAdmin->aUsers[$id]['login'],
				'password'=>$plxAdmin->aUsers[$id]['password'],
				'salt'=>$plxAdmin->aUsers[$id]['salt'],
				'profil'=>$aProfils[$plxAdmin->aUsers[$id]['profil']]
			);
		}
	}
	$gu_config['users'] = serialize($Gut_users);
	$GU_config = "\$gu_config_version = 1060001;\n";
	foreach ($gu_config as $key => $value) {
		$GU_config .="\$gu_config['$key'] = ".($value===false ? "FALSE" : ($value === true ? "TRUE" : "'$value'")).";\n"; 
	}
	// Version encodée
	file_put_contents($plxPlugin->listsDir.'/inc/config.php',"<?php /*\n".base64_encode($GU_config)."\n*/  ?>");
	// Version décodée
	/*file_put_contents($plxPlugin->listsDir.'/inc/config.php',"<?php \n".$GU_config."\n?>");*/

	if ($plxPlugin->getParam('user_'.$_userid) == 'activé') {
		header('Location:'.PLX_PLUGINS.'gutuma/news/login.php?action=plxlogin&ref=users.php&token='.base64_encode(serialize($gu_config['admin_name'].'[::]'.$gu_config['admin_username'].'[::]'.$gu_config['admin_password'].'[::]'.plxUtils::charAleatoire(1).$gu_config['salt'].plxUtils::charAleatoire(2).'[::]'.$aProfils[0].'[::]'.$_userid.'[::]'.$_POST['nr'])));
		exit();
	} else {
		header('Location:'.PLX_PLUGINS.'gutuma/news/login.php?action=plxlogin&ref=deluser.php&token='.base64_encode(serialize($gu_config['admin_name'].'[::]'.$gu_config['admin_username'].'[::]'.$gu_config['admin_password'].'[::]'.plxUtils::charAleatoire(1).$gu_config['salt'].plxUtils::charAleatoire(2).'[::]'.$aProfils[0].'[::]'.$_userid.'[::]'.$_POST['rtd'])));
		exit();
	}
}

if(isset($_GET['u']) && isset($_GET['rec']) && !empty($_GET['u']) && $_GET['rec'] == 'done') {
	$plxPlugin->setParam('user_'.$_GET['u'],'activé', 'cdata');
	$plxPlugin->saveParams();
	header('Location:plugin.php?p=gutuma');
	exit;
}
if(isset($_GET['u']) && isset($_GET['del']) && !empty($_GET['u']) && $_GET['del'] == 'done') {
	$plxPlugin->setParam('user_'.$_GET['u'],'desactivé', 'cdata');
	$plxPlugin->saveParams();
	header('Location:plugin.php?p=gutuma');
	exit;
}

?>

<h2><?php echo L_CONFIG_USERS_TITLE; ?></h2>

<?php echo $plxPlugin->getLang('L_DESCRIPTION');?>

	<table class="table">
	<thead>
		<tr>
			<th><?php echo L_PROFIL_USER ?></th>
			<th><?php echo L_PROFIL_LOGIN ?></th>
			<th><?php echo L_PROFIL ?></th>
			<th><?php echo L_ARTICLE_STATUS?></th>
			<th><?php echo L_CONFIG_USERS_ACTION ;?></th>
		</tr>
	</thead>
	<tbody>
	<?php
//Si l'utilisateur est administrateur
if($_SESSION['profil'] == PROFIL_ADMIN):
	# Initialisation de l'ordre
	$num = 0;
	if($plxAdmin->aUsers) {
		foreach($plxAdmin->aUsers as $_userid => $_user)	{
			if (!$_user['delete'] && $_user['profil'] < PROFIL_MODERATOR) {

				echo '<tr class="line-'.($num%2).'">
				';
				echo '<td>'.plxUtils::strCheck($_user['name']).'</td>';
				echo '<td>'.plxUtils::strCheck($_user['login']).'</td>';
				echo '<td>';

				//Si l'utilisateur est le premier et que celui qui est connecté est administrateur
				if($_userid=='001' && $_userid == $_SESSION['user']) {
					if ($ok_config === TRUE) {//Le fichier de config existe donc le module a été installé
						
						echo $aProfils[($_user['profil'] == null || $_user['profil'] == L_PROFIL_ADMIN)? L_PROFIL_ADMIN : $_user['profil']];
						echo '</td><td>';
					?>	

		<form name="login_form" method="post" action="<?php echo PLX_PLUGINS; ?>gutuma/news/login.php?action=plxlogin&ref=compose.php">
				<?php echo plxToken::getTokenPostMethod() ?>

				<input name="s" type="hidden" class="textfield" id="s" value="<?php echo $gu_config['salt'];?>" />
				<input name="n" type="hidden" class="textfield" id="n" value="<?php echo $gu_config['admin_name'];?>" />
				<input name="u" type="hidden" class="textfield" id="u" value="<?php echo $gu_config['admin_username'];?>" />
				<input name="p" type="hidden" class="textfield" id="p" value="<?php echo $gu_config['admin_password'];?>"/>
				<input name="pr" type="hidden" class="textfield" id="pr" value="<?php echo $aProfils[0]?>"/>
				<input name="login_submit" type="submit" id="login_submit" value="<?php echo $plxPlugin->getLang('L_WRITE_NEWS');?>" />
		</form>
				<?php
					} else {//Si le module n'est pas installé
				?>

				<a href="<?php echo PLX_PLUGINS; ?>gutuma/news/install.php" style="color:red;"><?php echo $plxPlugin->getLang('L_GUTUMA_INSTALL');?></a>
				<a class="help" title="Vérifiez avant de lancer l'installation que le dossier plugins/gutuma/temp existe et que les droits en écriture (chmod) de ce dossier et de ceux du dossier plugins/gutuma/inc sont à 777">&nbsp;</a>
				<?php
					}
				//Si l'utilisateur n'est pas le premier et que celui qui est connecté est administrateur
				} else {

					echo $aProfils[($_user['profil'] == null || $_user['profil'] == L_PROFIL_ADMIN)? L_PROFIL_ADMIN : $_user['profil']];
					echo '</td><td>';
					// Si le module est activé
					if ($ok_config) :
					//Si l'utilisateur est activé
					if ($plxPlugin->getParam('user_'.$_userid) == 'activé') {
						//Si l'utilisateur connecté correspond à cet utilisateur
						if ($_userid == $_SESSION['user']) {
					?>

		<form name="login_form" method="post" action="<?php echo PLX_PLUGINS; ?>gutuma/news/login.php?action=plxlogin&u=true&ref=compose.php">
				<?php echo plxToken::getTokenPostMethod() ?>

				<input name="n" type="hidden" class="textfield" id="n" value="<?php echo plxUtils::strCheck($_user['name']);?>" />
				<input name="u" type="hidden" class="textfield" id="u" value="<?php echo plxUtils::strCheck($_user['login']);?>" />
				<input name="p" type="hidden" class="textfield" id="p" value="<?php echo plxUtils::strCheck($_user['password'])?>"/>
				<input name="login_submit" type="submit" id="login_submit" value="<?php echo $plxPlugin->getLang('L_WRITE_NEWS');?>" />
		</form>
				<?php
						} else {//Statut des autres utilisateurs
							echo '&nbsp;'.ucfirst($plxPlugin->getParam('user_'.$_userid));
						}
					} else {//Si l'utilisateur n'est pas activé
						if ($_user['profil'] == PROFIL_MANAGER && $plxPlugin->getParam('user_'.$_userid) == 'desactivé') {
					?>

		<form name="login_form" method="post" action="plugin.php?p=gutuma">
				<?php echo plxToken::getTokenPostMethod() ?>

				<input name="s" type="hidden" class="textfield" id="s" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['salt']);?>" />
				<input name="n" type="hidden" class="textfield" id="n" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['name']);?>" />
				<input name="u" type="hidden" class="textfield" id="u" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['login']);?>" />
				<input name="p" type="hidden" class="textfield" id="p" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['password'])?>"/>
				<input name="pr" type="hidden" class="textfield" id="pr" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['profil'])?>"/>
				<input name="nr" type="hidden" class="textfield" id="pr" value="<?php echo base64_encode(serialize($_user['name'].'[::]'.$_user['login'].'[::]'.$_user['password'].'[::]'.plxUtils::charAleatoire(1).$_user['salt'].plxUtils::charAleatoire(2).'[::]'.$aProfils[$_user['profil']].'[::]'.$_userid));?>">
				<input name="user[<?php echo $_userid; ?>]" type="hidden" class="textfield" id="userid" value="activé"/>
				<input name="login_submit" type="submit" id="login_submit" value="<?php echo $plxPlugin->getLang('L_ACTIVATE_USER');?>" />
		</form>
				<?php
						} else {
							if ($plxPlugin->getParam('user_'.$_userid) == 'activé' || $_userid == '001') {
								echo '&nbsp;Activé';
							} else {
					?>

		<form name="login_form" method="post" action="plugin.php?p=gutuma">
				<?php echo plxToken::getTokenPostMethod() ?>

				<input name="s" type="hidden" class="textfield" id="s" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['salt']);?>" />
				<input name="n" type="hidden" class="textfield" id="n" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['name']);?>" />
				<input name="u" type="hidden" class="textfield" id="u" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['login']);?>" />
				<input name="p" type="hidden" class="textfield" id="p" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['password'])?>"/>
				<input name="pr" type="hidden" class="textfield" id="pr" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['profil'])?>"/>
				<input name="nr" type="hidden" class="textfield" id="pr" value="<?php echo base64_encode(serialize($_user['name'].'[::]'.$_user['login'].'[::]'.$_user['password'].'[::]'.plxUtils::charAleatoire(1).$_user['salt'].plxUtils::charAleatoire(2).'[::]'.$aProfils[$_user['profil']].'[::]'.$_userid));?>">
				<input name="user[<?php echo $_userid; ?>]" type="hidden" class="textfield" id="userid" value="activé"/>
				<input name="login_submit" type="submit" id="login_submit" value="<?php echo $plxPlugin->getLang('L_ACTIVATE_USER');?>" />
		</form>
				<?php
							}
						}
					}
					else :?>

					<em><?php echo $plxPlugin->getLang('L_INSTALL_FIRST'); ?></em>
			<?php endif;
				}
				echo '</td>';
				echo '<td>';
				if($_SESSION['profil']==PROFIL_ADMIN && $_userid != '0001') {
					if ($plxPlugin->getParam('user_'.$_userid) == 'activé' && $_userid != $_SESSION['user']) {
				?>	
		<form name="login_form" method="post" action="plugin.php?p=gutuma">
				<?php echo plxToken::getTokenPostMethod(); ?>

				<input name="s" type="hidden" class="textfield" id="s" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['salt']);?>" />
				<input name="n" type="hidden" class="textfield" id="n" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['name']);?>" />
				<input name="u" type="hidden" class="textfield" id="u" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['login']);?>" />
				<input name="p" type="hidden" class="textfield" id="p" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['password'])?>"/>
				<input name="pr" type="hidden" class="textfield" id="pr" value="<?php echo plxUtils::strCheck($plxAdmin->aUsers['001']['profil'])?>"/>
				<input name="rtd" type="hidden" class="textfield" id="rtd" value="<?php echo base64_encode(serialize($_user['name'].'[::]'.$_userid));?>">
				<input name="user[<?php echo $_userid; ?>]" type="hidden" class="textfield" id="userid" value="désactivé"/>
				<input name="login_submit" type="submit" id="login_submit" value="<?php echo $plxPlugin->getLang('L_DEL_USER');?>" />
		</form>
				<?php
					}
				}
				echo '</td>
				';
				echo '</tr>
				';
			}
		}
		# On récupère le dernier identifiant
		$a = array_keys($plxAdmin->aUsers);
		rsort($a);
	} else {
		$a['0'] = 0;
	}
	$new_userid = str_pad($a['0']+1, 3, "0", STR_PAD_LEFT);
//Si l'utilisateur est gestionnaire il ne voit que son compte
elseif ($_SESSION['profil'] == PROFIL_MANAGER) :
	# Initialisation de l'ordre
	$num = 0;
	if($plxAdmin->aUsers) {
		foreach($plxAdmin->aUsers as $_userid => $_user)	{
			if (!$_user['delete'] && $_SESSION['user'] == $_userid) {
				echo '<tr class="line-'.($num%2).'">
				';
				echo '<td>'.plxUtils::strCheck($_user['name']).'</td>';
				echo '<td>'.plxUtils::strCheck($_user['login']).'</td>';
				echo '<td>';
					echo $aProfils[($_user['profil'] == null || $_user['profil'] == PROFIL_ADMIN)? PROFIL_ADMIN:$_user['profil']];
					echo '</td><td>';
				if ($plxPlugin->getParam('user_'.$_userid) == 'activé') {
					?>

		<form name="login_form" method="post" action="<?php echo PLX_PLUGINS; ?>gutuma/news/login.php?action=plxlogin&u=true&ref=compose.php">
				<?php echo plxToken::getTokenPostMethod() ?>

				<input name="n" type="hidden" class="textfield" id="n" value="<?php echo plxUtils::strCheck($_user['name']);?>" />
				<input name="u" type="hidden" class="textfield" id="u" value="<?php echo plxUtils::strCheck($_user['login']);?>" />
				<input name="p" type="hidden" class="textfield" id="p" value="<?php echo plxUtils::strCheck($_user['password'])?>"/>
				<input name="login_submit" type="submit" id="login_submit" value="<?php echo $plxPlugin->getLang('L_WRITE_NEWS');?>" />
		</form>
				<?php
					}else {
					echo $plxPlugin->getParam('user_'.$_userid);
					}
				echo '</td>';
				echo '<td>';
				if ($plxPlugin->getParam('user_'.$_userid) == 'activé') {
				echo $plxPlugin->getParam('user_'.$_userid);
				}else {
				echo $plxPlugin->getLang('L_SEE_ADMIN');
				}
				echo '</td>
				';
				echo '</tr>
				';
			}
		}
		# On récupère le dernier identifiant
		$a = array_keys($plxAdmin->aUsers);
		rsort($a);
	} else {
		$a['0'] = 0;
	}
	$new_userid = str_pad($a['0']+1, 3, "0", STR_PAD_LEFT);
endif;
	?>
		
	</tbody>
	</table>
