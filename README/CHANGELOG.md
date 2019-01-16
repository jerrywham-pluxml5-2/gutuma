Gutuma : CyberLettres pour PluXml ;)

A la possibilité d'intégrer subscibe.php l'apparence du site PluXml qui l'héberge, belle idée...
	#C'est_fait* avec une page statique, *voir l'aide ;)
	Plusieurs pistes :
		*avec une page statique et un <?php include() ?> ou 
		alors une frame en html, mais il y a un hic, car les @mails envoyés par gutuma auront l'url standard!

		*Une option du coté des réglages "[i]url de la page publique de gestion des abonnements (subscribe.php)[/i]"
	 *qui, si présente change l'adresse url des lettres des (dés)inscriptions semble faire l'affaire. 
		Il y a même la possibilité d'activé une page statique ou de créer un hook a appeler (config du plugin (standard) a créer et ça complique les réglages).
		@ voir ...


TODO :
Pourquoi les variables de langues sont en session?
++Une fois le timer de la notification javascript dépassé l'affichée (mais ou) pour qu'elle soit toujours visible pour l'internaute.
---Peut-être remanier le texte sur les pourriel pour y adjoindre "et ajouter [emailProtetégé] a votre carnet d'adresses" NON (les internautes ne le font jamais, et semble ne servir a rien) ::: chouette du taf en moin

Supprimer un courriel global, qui l'efface dans toutes les listes (lists.php)
+ 1 profil d'éditeur? (2 possible pour le moment) et/ou le manager peut gérer les gadjets.
Un param query du formulaire (subscribe) (choix du theme de gutuma)
Hook : (in plxAdminBar?) Proposer d'envoyer l'article, page statique a son édition.
Gérer les tableaux avec datatablejs (vanilla) !?
Historique des cyberlettres avec le liens envoyés et visibles sur le site. (si ok send.lock ==> send.ok)
Si pluXml est en mono utilisateur : autoinstall/connect ???
Le smtp_password est à crypté dans la conf, et décrypté pour l'envoi des news, prévoir la MAJ ou prévenir l'admin. (reverse engineering protect)
eval($plxAdmin->plxPlugins->callHook('AdminTopMenus'));
Evol: swiftmailer-5.4.6 pour future integration http://swiftmailer.org
& si theme default -> use static mode & display (site thème au lieu de celui d'admin) ou add gutuma logo (top left|center) [une iframe]
?Prévoir en cas de suppression d'un utilisateur, supprimer le param (user activé ou désactivé)
Possibilité d'utiliser les plugins éditeur de texte Tiers (WYSIWY(M|G)) a la place de celuis inclus (TinyMce) Pour plus de possibilité et de réglages (accés aux medias manager, ...)
?Rediriger login.php de gutuma vers plugin?p=gutuma (s'affiche lorsque l'on désactive / réactive le plugin et que l'on se rend sur compose, newsletters.php ,...)
AdminSettingsAdvanced plxAdminEditConfiguration ... data/gutuma set in PluXml advanced params*
*Hook gadget.js.php auto inclus dans la partie site
*public function ThemeEndBody(){#auto gadgets
*#PHP echo "\n\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'gutuma/news/js/gadgets.js.php"></script>';
*}
Message Oups went wrong ::: mauvaise clé abonné ou non, mauvaise liste (id) ... une erreur inconnu quoi...
si array_unique of time ²opt (just one link to gérér subcribtion)
? changer ou enlever le i. en time des listes temporaires (var algo) ::: clé ds config (sécu)
Notifs : si nojs les rendre display:block (class nojs ?) (si js rem class nojs)
Gadget : utiliser les "placeholder" pour remplacer ceux en js (hint)

#Curieux mais normal
Si mail non inscrit dans liste reele mais dans tmp, si utilise keycode pour désinscrire = envoie de mail érroné; ne s'est réellement désinscrit, il n'était pas ds la liste réele);
Possible que l'inverse se produise, si ds liste réele et ds tmp; si keycode utilisé pour inscrire = envoi de mail de confirm & notif? En effet OUI.
#Ptet modifier le courriel (& MESSAGE SYS) en stipulant (vous etiez déja (dés)abonné, vous avez effacé votre courriel de la liste [d'accueil] temporaire

#idées:
	si auto save actif : post-it last saved time
	#replay sess (add list id OR not to add new address)
	(edit)list(s)
		(reload buttons)
		tempo : on clic on email go to search in real (with filter ;)
	subscribe.php :::
		#TODO : to add another friends reset address #PHP:   $list->get_name()   absolute_url('subscribe.php').'?list='.$list->get_id().'&action=subscribe
		Un wall-e?
#+ misc.js
function hideShow(id,o){//used for show keycode in admin ::: todo for statusmsg AND errormsg ???
 o = o?o:'';//inline-block
 if (document.getElementById(id).style.display==o){
  document.getElementById(id).style.display='none';
 }
 else{
  document.getElementById(id_div).style.display=o;
 }
}


#INCOMPATIBILITÉES :
CSSNOCACHE (DOUBLE INSTANCE ERREUR)

#NOTES :
Parametres d'url *(du formulaire d'inscription) :
 Pour débogger : &DEBUG=1
*Pour changer la langue : &l=fr
*Pour cacher le lien "retour a l'accueil" : &backlink=no
Chez free (php 5.1) a chaque envoi d'une lettre = Impossible de verrouiller l'infolettre. N'en tenez point compte, ceci est du au LOCK_EX


Variables de langues sont en session ['glang']
http://postmaster.free.fr/
//number_format($number, $decimals, ",", "")

is_post_var('msg_html')#aperçu
is_post_var('msg_text')text aréa editor name (compose.php & ailleurs ... )

+ aide aux traducteurs (comment tourne le système de fichier de langue) :
le fichier de langue se trouve a l'interieur du dossier gutuma/news/lang/
Dupliquer le fichier fr et le rennomer (es, oc, ru, ro, ...)
Le texte qui précede ces 6  [::->]  caractères sont la clé (en anglais). Après ceux-ci se trouve la traduction.
Gutuma Newsletter Management[::->]Gestion des Infolettres
Les cles anglaises sont liées aux système de gutuma, si vous souhaitez les changer, veulliez les changer partout ou leurs textes (original) sont (en général dans les themes, mais ils peuvent être parteout dans le codes, et le ficher est divisé en parties. An aparté : les commentaires (//) sont possible a chaques début de ligne

+ aide aux intégrateurs :
#Multi_Pluxml et #même_domaines (localhost/plx1/, localhost/plx2/) :
#SI les #dossiers gutuma du plugin sont #symboliques et qu'il vous est impossible d'acceder au module d'infolettre a partir de certains PluXml!
·le fichier "news/inc/_pluxml.php" est a modifier :
·Commenter en ajoutant un dièse [#] au début de la ligne 38
·Décommenter en supprimant les dièses [#] au début des lignes 35, 36 et 37
·Cela change la définition de la racine de pluxml : PLX_ROOT
·#$gu_sub = str_replace($_SERVER['DOCUMENT_ROOT'].__GDS__,'',$gu_sub[0]);#4 found subdir where plx is


<script type="text/javascript">
	setMsge('errormsg');
	setMsge('statusmsg');//il devrait etre subscribe.php 5.3.1 setMsge('statusmsg','mvto');  ?????????
</script>

TD
+ Si url subscribe est différente, afficher un lien "voir" (logic in param + see link(s) in themes
+ lien aide du plugin (page info)
+ Si session gutuma actif, lors du clic (admin -> redirect to compose ?
+ 1 Page statique. Dans admin (1 lien parametre d'intégration du module gutuma -> confiXml Activer la page statique du plugin, Si connecté une fois tjrs goto compose on clic in PluXml menu (admin)

#TEP
Rétablir: subscribe PLX_GROOT + rem var_dump et réactiver les envois

===============================CHANGELOG================================

## v2.2.0 16/01/2019 ##
[+] composer lettre* : Auto sauvegarde amélioré (compose.php + overlay)
[+] *media manager : gestionnaire de media natif de PluXml pour l'insert d'images et de médias (tinyMCE de compose.php)
[+] *Langue : L_NO_JS_STORAGE : classe du plugin gutuma.php : pour le systeme du gestionnaire de media (tinyMCE de compose.php)
[+] *MAJ : Tinymce : 4.7.10 (2018-04-03) ==> 4.9.0 (2018-11-27)
[+] *codemirror.5.42.2 (21-12-2018) (tinyMCE de compose.php)
[+] Aide (fr) : Fichier d'exemple (Une page statique a copié) + conseils peaufinés
 : : page statique subscribe est compatible avec la réécriture d'url :)
[+] subscription.php
 : : Messages de la première (fr & en) retouchés (1st In/Out messages)
 : : Fins de ligne unifiés des message (courriels) : $EOL = "\r\n" + $HR (72 =) 
 : : Moins de sauts de ligne
 : : Norme des courriels : Fin de ligne des messages de LF à CRLF (\n --> \r\n)
Fix inc/_pluxml.php : si connecté avec un compte inferieur a gestionnaire : ajax, gadget et subscribe(.php) Bloqué (erreur FrontEnd)
Fix : image du menu theme 5.3.1 bonne taille avec compose.php, non les autres menu :: graçe a sa règles css "inline" img {height: auto !important;max-width: 100% !important;} theme default 5.3.1 
Fix : Warning: is_dir(): open_basedir restriction in effect. File(..) is not within the allowed path(s) : in gutuma/news/inc/list.php on line 367
 			  if ($file[0] != ".") //fix Warning: is_dir(): open_basedir restriction in effect. File(..)
Fix : Notice: A non well formed numeric value encountered in gutuma/news/inc/list.php on line 368 and on line 278
https://forum.pluxml.org/viewtopic.php?pid=57616#p57616

## v2.1.0 01/10/2018 ##
[+] Languages peaufinés, messages plus clairs (notifs et courriels)
[+] Subscription, & subscribe(s) (+ simple)
[+] Algo : systeme de ré-envois & POST keycode : option supprimé
[+] 3 réglages en plus : show_home_link, subscribe_help & subscribe_url (onglet général)
    : subscribe_url change tous les liens des courriels (utilisateurs & admins)
[+] Nouveau paramètre d'url &help=no en plus du &backlink=no
[+] Inclure dans une autre page avec php est maintenant possible, voir l'aide du plugin ;)
[+] Code d'exemple inclu : static_include_code.txt dans fr-help.php
[+] themes/default/css/gutuma.css Renommé en style.css
[+] Les #msg (notif js) sont copiés dans un paragraphe dédié pour rester affiché a l'internaute
    : $mvto est "l'id" ou sera copié le(s) #msg : function gu_theme_end($mvto='')
[+] Merci @jack31 qui a permis de simplifier les messages et son comportement


## v2.0.1 28/09/2018 ##
[+] Messages des notifs peaufiné (subscribe form)
[+] L'aide est plus explicite (subscribe form)
[+] Réglages Généraux : Typo + url de contact pour le lien de l'aide (subscribe form)
Fix Réglages Transport : Message Test Notif erreur de trad : scbb @jack31 : https://forum.pluxml.org/viewtopic.php?pid=57568#p57568


## v2.0.0 23/09/2018 ##
[+] Double confirmation (²opt in/Out)
[+] ²opt : dbl verif (si ds opt et reelle)???
[+] ²opt : resend key ::: veta dak
[+] Roule en php.5.1.3RC4-dev (free.fr) fixed :
    Warning: array_walk() [function.array-walk]: Unable to call SELF::get_tmp_address() in plugins/gutuma/news/inc/list.php on line 101
  		  array_walk($addressesStr, array('self', 'get_tmp_address'));#'SELF::get_tmp_address()' : FIX Warning: array_walk() [function.array-walk]: Unable to call SELF::get_tmp_address() ::: THX ezhacher at gmail dot com : https://php.net/manual/en/function.array-walk.php#115622
[+] Cron maison dans la func get (classe list) qui MAJ la liste tmp si trop vieux courriel ::: OLD idea ::: un bouton wash (editlist) pour effacer les mails tempo (anciens/ tous?) (et)ou un autowash temporel (ds la function)
[+] subscribe.php : Le formulaire peut changer de langue avec un parametre d'url : ?l=fr ;) Exemple : /plugins/gutuma/news/subscribe.php?backlink=no&l=en
[+] Aide explicative au formulaire basique car tous le monde y passe (clé de validité)
[+] gu_error('<br />'.  ::: 4ll
[+] Themes : gutuma est fluide (480 -> 700px), default (petites retouches)
[+] Tableau des liste trié par ordre alphabètique
[+] Afficher ou cacher les clés de validité dans l'admin
[+] Souscription basique : renvoi les clés hash (key code) (case a cocher)
[+] Efface k_sbscribe afin que le champ k soit vide aprés la ²opt
[+] Lors de la mise a jour, créer les fichiers temporaires (si absents) ::: gu_update() ::: #csv_fix by clone()
[+] settings (#tep default, #tep gutuma) : 15, 20, 30, 45, 60, 90 jours de rétention des email temporaires ::: gu_theme_list_control($setting_name,$options,$control=FALSE,$attrs='')
[+] Message(s) lors de la premiere (dés)inscription avec la(es) clé(s) //send 1st opt mail with validate key code(s) and user notice:::BEP
[+] Effacer les adresses des espaces temporaires au dela de 15/30/60/90 jours (config?) +? si clic sur souscrire?
[+] Admin lists + les courriels en transit
[+] Admin editlist interfaces des courriels en transit + icones d'info
[+] Admin editlist (temporaires) : permet l'envoi d'un courriel avec les instruction et le keycode (de validité) d'un clic (icone).
[+] Messages lang singuliers et pluriel
[+] Souscription basique : les listes sont toute cochées par defaut
[+] Themes subscribe : si aucune liste dispo, l'affiche
[+] Gadget : formulaire classique : ajout de l'indice (placeholder d'antan)
Fix Page informations (index.php) : MAJ bloqué js chez github par le Navi (CORS policy) : déplacé chez free.fr ;)
Fix Impossible de lire le fichier de la liste ... &+???
Fix Les projets (compos) ont disparues de l'écran (strpos >= 0 vers !== FALSE)
Fix Lors d'un import csv (duplique les adresses dans la liste 'i' ??? et sans le time();
Fix Add address in list (private OR not) (admin)
Fix Lors du renomage d'une liste (renommer la tempo)
Fix Si noscript impossible redemandé la clé hash de validité.
Fix Test réglages smtp.free.fr : swift non instancié = Fatal error: Call to a member function disconnect() on a non-object in gutuma/news/inc/mailer.php on line 230


## v1.9.1 07/07/2018 ##
[+] La config de gutuma change si un admin ou un gestionnaire modifie s(l)es identifiants
[+] Écrire une infolettre : Placeholder explicatif dans le champ pour le sélecteur de liste
[+] subscribe.php?list= ... &backlink=no # @$_GET["backlink"]=='no'?' style="display:none"':'' # Cache "retour a l’accueil" sur les 2 thèmes ;)


## v1.9.0 19/05/2018 ##
[+] Compatibilités : 5.2, 5.3.1, 5.4, 5.5 & 5.6+
[+] [compose] tinyMCE 4.7.10 customisé (browser_spellcheck: true)
[+] [style du Theme default] classes ajouté aux menus + compatible avec les themes admin de PluXml (pluCss class : menu) + logo gutuma
[+] Nom du Menu d'admin est "cyberlettres", peut être changer, si besoins est. Vient du fichier de langue, plus admin|user friendly :)
[+] Position du Menu : remplacer 0 par tout autre chiffre change sa position (classe du plugin : gutuma.php a la ligne 16) * En rapport avec d'autre Plugins :)
[+] Sécurité : les profils évoluent en temps réel + plxAdminEditUsersXml réécrit
    [administrer les utilisateurs du plugin] verifie s'il est activé a chaque pages
      Fix : lors de la désactivation d'un utilisateur, si celui-ci est connécté, il peut le rester tant qu'il reste dans gutuma.
      Vérifie cnx users désactivé ou supprimés de pluxml(no admin menu?)
      Si perte du cookie de PluXml et accés a compose par exemple (tjrs connecté a gutuma) [vérifie si connecté dans PluXml]
[+] Auto sauvegarde du projet (5 min par defaut, réglage sessionnaire)
[+] Lien d'install du module couleur orange (+compatible avec les anciens PluXml)
[+] news/inc/theme.php : ajout de possibilité d'attributs : fonction gu_theme_list_control
[+] [admin] cache les drapeaux de plxMyMultilingue (css)
[+] [themes] Nouveau systeme de boutons des pourboires, news/inc/index.tips.inc.php : Liberapay
    (le formulaire Paypal de l'auteur original (gutuma 1.6), Rowan Seymour, y est décommentable)
Fix [install] Si non connecté dans PluXml lancement de l'install puis redirigé vers l'accueil de l'admin lors du clic sur "connexion" a Gutuma
Fix [admin] Liste les utilisateurs activés sont les seuls affichés
Fix [compose] tinyMCE : editor.css tjrs du theme gutuma : content_css: 'themes/<?php echo gu_config::get('theme_name') ?>/css/editor.css
ren public static function set_adehsion($key) en set_adhesion


## v1.8.7.plx.5.6 16/03/2018##
fixé Unable to open message file[::->]Impossible d'ouvrir le fichier de message (surment car le dossier (vide) est tjrs présent
 erreur de fileLock exclusive flock in free.fr 
 Unable to lock newsletter recipient list[::->]Impossible de verrouiller la liste des destinataires de l'infolettre
 lors de l'envoi de newsletter
 http://manpagesfr.free.fr/man/man2/flock.2.html
 Un appel flock() peut bloquer si un verrou incompatible est tenu par un autre processus. Pour que la requête soit non bloquante, il faut inclure LOCK_NB (par un OU binaire « | » ) avec la constante précisant l'opération. 
[+] suppression des tmp free 
[+] misc(.min).js gu_browser_keep_save_pass() & call onsbmit setting form 2 block savepassbrowserbox
[+] function gu_theme_password_control($setting_name,$option=false) add option 4 add autocomplete="off"
[+] gu_sender_test error return system (swift 4 have no multiple?)
[+] debugmsg default theme
[+] Retouche légère du style des boutons principaux
[+] Redirige vers la page "erreur" tout appel a une page de Gutuma (/news/*) si le plugin est désactivé
[+] Hook admin si dans l'admin avec PLX_ADMIN
[+] Infobulle du Menu Admin plus explicite (user friendly) // Change tooltip menu with lang var
[+] public static function set_adehsion($key)
Fix: gutuma settings ::: Test Sendmail never called (FALSE O LIEU DE TRUE)
Fix: _settings pass smtp visible: theme gutuma ::: gu_theme_[text|password]_control()
Fix: misc.php check_email compatible free.fr (filter_var() absente)
Fix: Double News au lien de confirmation d'inscription (Merci Jack31) ::: http://forum.pluxml.org/viewtopic.php?pid=55753#p55753
Fix: Free [PHP.5.1.6] utilise plxUtils::checkMail() si filter_var() indéfinie + remise en route du filtre originel de Gutuma en dernier recours.
Fix: Notices PLX_MULTILINGUE déjà définie lors de l’appel a getInstance de plxMotor [plxMyMultilingue]
Fix: Mauvaise url du fichier css personnel [custom_admincss_file]
Fix: gadget.js gu_init(FALSE, FALSE); (3 FALSE au lieu de 2) Initialize Gutuma without validation or housekeeping

## v1.8.6.plx.5.6  09/09/2017##
[+] Retouche legere des notifs
[+] GUTUMA_ENCODING = PLX_CHARSET
[+] Les antislash (\) remplacés par des slashs (/) dans les variable textes de config ::: Les antislash (qui sont contre le guitariste) sont remplacé par des slashs (qui sont pour) afin de proteger le fichier de config lorsque l'on applique de nouveaux paramétres ::: Par ex.: Si le nom de l'application était My-NewsLetter\ cela rendait la lecture du fichier de config impossible, voir ci-après
Fix: Serveur win:
:::: La racine ($gu_config['ROOT']) pose probleme après l'install du module, la derniere apostrophe est echapé par un antislash qui empeche d'accédé au reste du code évalué de $gu_conf (ici user) #*# $gu_config['ROOT'] = 'E:\htdocs\PluXml-5.6\myPluXml\plugins\gutuma\news\'; #*# (merci cpalo)
:::: $gu_config['ROOT'] inutile et pose probleme sous windows commenté (todo: a supprimé dan setting.php)


## v1.8.5.plx.5.6  02/09/2017##
[+] Thèmes & langues retouchés + d'autres subtilités
[+] Canal de mise a jour officiel lié au dépôt github ::: https://raw.githubusercontent.com/jerrywham-pluxml5-2/gutuma/master/news/up_git.js ::: origine v1.6 => http://gutuma.sourceforge.net/update.js.php
[+] Convertisseur html_to_text peaufiné
[+] Arrive direct sur la liste des courriels lorsque l'on change de page (Ajout d'une ancre aux liens << < > >>)
[+] Paramètres TinyMCE: Désactivé, Barre d'outils (défaut), Menu ou Les 2 ensemble
[+] Paramètres TinyMCE: Correcteur orthographique du navigateur (activé par défaut)
Fix: affichage: lors du choix du menu "informations" (index.php) le menu article de plux est actif (en plus de celui d'info) ::: option highlight du lien désactivé (merci PluXml)
Fix² _pluxml.php crash a l'install du module ::: http://forum.pluxml.org/viewtopic.php?pid=55327#p55327
:::: Mauvais chemin racine si PluXml est dans un sous dossier
:::: Mauvaise config de PluXml & gutuma chargé si le dossier du plugin est symbolique (par Exemple, est en réalité dans un autre PluXml) voir l'aide pour faire les modifs nécessaires au fichier news/inc/_pluxml.php ;-)

## v1.8.4.P56  01/09/2017##
[+] tinyMCE en v4.6.6 + la langue de l'éditeur est celle de l'utilisateur + plugins activé
[+] jsHash mis a jours en 2.2 ::: http://pajhome.org.uk/crypt/md5/scripts.html
[+] js minifiés avec jscompress.com et utlisés
Fix Le fichier de config est écrit en clair un bref instant sur le serveur pendant la sauvegarde des paramétres (core)
Fixs _pluxml.php crash a l'install du module ::: http://forum.pluxml.org/viewtopic.php?pid=55297#p55297
:::: Mauvais chemin racine (1ere idée pour dossier symbolique corrigé)
:::: Serveur autre que Tux
:::: exec interdit en safe_mode (PHP) (Par defaut ds WAMP) && __LINK__ supprimé (inutilisé)

## v1.8.4.plx.5.6  30/08/2017##
[+] Admin thème 'default' + adapté a PluXml 5.6 & + coloré + responsive (stratégie mobile 1st)
[+] Admin Manager Auto connect ::: Les gestionnaires accèdent a l'interface pour composer une infolettre en JavaScript
[+] Admin choix des thèmes par sélecteur listant les dossiers présent (gutuma & default)
[+] admin Langue anglaise ajouté [todo: help]
[+] admin.php simplifié (code & algo) more dev&user friendly
[+] Français actualisé pour Admin, Aide et Gutuma (news)
[+] Listes et config déplacé du plugin vers data/gutuma/
[+] Dossier temporaire déplacé du système vers data/gutuma/tmp (projets d'infolettres)
[+] Fins de lignes optim 4 unix with dos2unix (thx BaZooKa07)
[+] Codes révisé et optimisé pour plus de performances (server parseless do more, chasse aux espaces superflus)
[+] Options d'import de liste en csv: ignorer la première ligne du fichier, selecteur de séparateur (; ou ,) + adresses dédoublonnées par array_unique
[+] editlist pagination & Navi Amélioré (0 addrs ds list [js gu_ajax_on_remove_address()]) (recharger"editlist" même page ou précédente si dernière page)
 FixOf: si toutes les adresses de la page supprimés on reste là planté là
[+] Peut être un dossier symbolique : ajout dans inc/_pluxml.php de PLX_GROOT, PLX_MORE & __FILE__ ::: duckduckgo.com/?t=lm&q=PHP+SOLVE+FOLDER+SYMLINK&ia=qa
 FixOf: Si le dossier du plugin est un lien symbolique erreur de chargement des librairies & de la config de PluXml (If plugin is in Symlink folder)
Fix Lien classique mauvaise url de retour a l'accueil news/(integrate & subscibe).php (Lien classique)
 Fix: gadget.js.php ::: Bug: Lien classique envoie au mauvais endroit (manque news/ ds l'url) [admin]
Fix Variables de langues déjà présentes dans le cœur de PluXml (Supprimées)
Fix Charger la langue de gutuma en fonction de celle de l'utilisateur
Fix Si apostrophes dans variable texte === config crashé ::: change in texts vars (') simple quote &#39; to Right single quotation mark &#146; &rsquo; (’)
Fix Thème gutuma subscribe.php: affiche le lien retour a l'admin du plugin (lien retour a l'accueil maintenant si non connecté a PluXml)
Fix Aucune redirection si un admin active un utilisateur ::: "login" 2 "name" user 4 var create token in /admin.php & post 2 news/users.php
Fix Thème gutuma (si PluXml non installé, retour)
Fix Doubles quotes in html attributes created with js
Fix Cacher le menu admin si non auth
Fix Souscription si guest user : fichiers ajax, js et subscribe inaccessibles car redirigé a la cnx admin :/
Fix Admin header 404 + mode erreur si ? dans url ou sinon en mode home : suppression des appels au préchauffage() et au démarrage()
Fix Verif config modifiable (themes/*/_index.php) 'inc' to GUTUMA_CONFIG_FILE

## v1.8.4.plx.5.3.1  02/03/2017##
[+] certaines redir (complexes) doublé d'un méta refresh (pb de not found 404)
[+] fixé lors de certaines opération (js), ne recharge pas la page (champs non mis a jour) (suppression liste,brouillon, ...)
[+] fixé Liste des utilisateurs validés (affiche plusieurs fois (2) les utilisateurs) in settings (Only in default theme)
[+] fixé swift 3 Stricts standards: Only variables should be assigned by reference [php 5.5.6](swift en version 3.3.2)
[*]formulaire ajax (place holder(s)) & value undefined (after demande...) [résolu] !attention aux id
