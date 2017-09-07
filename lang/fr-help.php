<?php 
/**
 * Fichier d'aide pour gutuma
 *
 * @version 1.8.5
 * @date	29/10/2012 * @author	Cyril MAGUIRE
 * @date	02/09/2017 * @author	Thomas Ingles
 **/
if(!defined('PLX_ROOT')) exit; ?>
<p class="in-action-bar">Aide du plugin Gutuma, le gestionnaire de Newsletters</p>
<h3>Installation</h3>
<p>Le plugin est constitué de deux dossiers principaux:</p>
<ol>
<li>le dossier <code>gutuma</code> qui doit être placé dans le dossier des plugins de Pluxml</li>
<li>le dossier <code>news</code> contenu dans le dossier <code>gutuma</code> est en réalité Gutuma lui même adpté pour PluXml ;-)</li>
</ol>
<p style="color:orange;">Les dossiers et fichiers suivants doivent être éditables :</p>
<p>Dans la mesure du possible, le dossier gutuma et tout ses composés sont créés lorsque le premier admin clique sur <a class="button green" style="color:yellow;" href="#active">Installer le module</a></p>
<ul>
<li><code>data/gutuma/</code></li>
<li><code>data/gutuma/tmp/</code></li>
<li><code>data/gutuma/inc/config.php</code> (ce fichier est créé lors de l'installation du module et <a href="#config">contient les paramètres de Gutuma</a>)</li>
</ul>
<h5>L'adresse courriel du premier compte admin (001) doit être renseignée.</h5>
<p>&nbsp;</p>
<h3 style="color:red;">IMPORTANT POUR LA MIGRATION (POUR RETROUVER LES ANCIENNES LISTES DE DIFFUSION</h3>
<h4 style="color:red;">DE LA V1.2 A LA V1.8.5.plx.5.6</h4>
<ol>
<li>Copier tout le contenu de l'ancien dossier plugins/gutuma/news/lists (celui à la racine de pluxml) dans le dossier data/gutuma/</li>
<li>Remplacer l'ancien dossier gutuma par le nouveau dans le dossier plugins</li>
<li>Renommer le dossier data/gutuma/temp en data/gutuma/tmp</li>
<li>Verifier les droits data/gutuma/*/*</li>
</ol>
<h4 style="color:red;">DE LA V1.1 A LA V1.8.5.plx.5.6</h4>
<ol>
<li>Remplacer l'ancien dossier gutuma par le nouveau dans le dossier plugins</li>
<li>Copier tout le contenu de l'ancien dossier /news/lists/ (celui à la racine de pluxml) dans le dossier /data/gutuma/</li>
<li>Renommer le dossier data/gutuma/temp en data/gutuma/tmp</li>
<li>Supprimer le dossier /news (celui à la racine de pluxml)</li>
<li>Verifier les droits data/gutuma/*/*</li>
</ol>
<p style="color:red;">Ps: ceci est supposé, il est possible que votre précédente architecture de dossier soit autre.</p>
<p id="active">&nbsp;</p>
<h3>Activation</h3>
<p>Pensez &agrave; activer le plugin dans le gestionnaire des plugins de Pluxml.<br/>
Seuls les administrateurs et les gestionnaires peuvent utiliser le plugin.</p>
<p>Les administrateurs ont accès aux réglages, tandis que les gestionnaires ne peuvent que gérer les news et les listes d'envoi.</p>
<p>Le module doit être installé <span style="color:red;">par l'administrateur principal (le numéro 001) du site</span> (<strong>Important !</strong>).</p>
<p>&nbsp;</p>
<p>Une fois le plugin activé, un lien "Gutuma" apparait dans la sidebar du panneau d'administration. Un clic sur ce lien dirige vers une liste d'utilisateurs susceptibles de pouvoir utiliser le plugin (les administrateurs et les gestionnaires). Par défaut, ils sont tous désactivés , sauf le premier utilisateur.</p>
<p>Pour poursuivre l'installation, ce dernier doit cliquer sur <a class="button green" style="color:yellow;" href="plugin.php?p=gutuma">Installer le module</a>.</p>
<p>Il est alors dirigé vers la page d'installation du module de news. Les informations affichées sont modifiables. Un clic sur le bouton <code>installer</code> termine l'installation.</p>
<p>L'utilisateur principal doit alors activer son compte en se connectant avec les mêmes identifiants que ceux utilisés pour la connexion au panneau d'administration de Pluxml.</p>
<p>Une fois ces quelques manipulations effectuées, les autres utilisateurs peuvent être activés à leur tour.</p>
<p id="symbiolink">&nbsp;</p>
<h3>Symbiolink</h3>
<p>Si le dossier de votre gutuma est symbolique (symlink) et qu'il se trouve etre dans un autre PluXml installé.</p>
<p>Ouvrir le fichier <code>news/inc/_pluxml.php</code> et décommenter les lignes 28 a 30 et commenter la ligne 32.</p>
<p>Cette option change la racine pour permetre a gutuma de charger les bons parametres (et non celle du PluXml ou gutuma se trouve en réalité).
<p><a href="http://forum.pluxml.org/viewtopic.php?pid=55297#p55297">Elle fait suite à des retours de bug</a> et peut être utile pour d'autre config.</p>
<p style="color:green;">Tenir aucun compte de ce paragraphe si gutuma s'active bien :-)</p>
<p>&nbsp;</p>
<h3>Utilisation</h3>
<p>Les administrateurs peuvent rédiger des news, gérer les utilisateurs, les listes de diffusion et les réglages du plugins. Les gestionnaires ne peuvent que rédiger les news et gérer les listes de diffusion.</p>
<p>Après avoir cliqué sur <code>Gutuma</code> puis sur <a class="button green">Accéder au module d&#039;infolettre</a>, une liste d'options s'affichent dans la sidebar, sous le menu Gutuma. Ce sont des liens vers les différentes fonctionnalités du plugin.</p>
<p>Les gestionnaires après avoir cliqué sur le menu <code>Gutuma</code> de la sidebar; si leurs javascript est actif; accedent en direct a <code>composer une infolettre</code>.</p>
<p id="config">&nbsp;</p>
<h3>Configuration</h3>
<p>La configuration se fait en mode administrateur uniquement. C'est l'option <code>Réglages</code> du menu de la sidebar qui permet de faire les modifications. Les champs sont suffisamment explicites et ne seront pas détaillés ici.</p>
<p>Un panneau d'informations sur l'installation et les paramètres système est également disponible.</p>
<h3>Gadgets</h3>
<p>Pour permettre aux lecteurs du site de s'abonner aux newsletters, des codes javascripts sont à placer sur la partie publique du site, dans le code source. Différentes possibilités sont offertes : affichage de liens, de bouton ou de champ de formulaire.</p>
<p>Pour mettre en page ces codes d'abonnement, des index css sont disponibles :  #suscribe-link pour les liens basiques et ajax, et #gu_subscribe_form pour les formulaires.</p>
<h3>Service Après Téléchargement</h3>
<p>Une idée, un comment on fait, un caillou dans les roues, Allez voir sur le <a href="http://forum.pluxml.org/viewtopic.php?id=3358">fil officiel du forum PluXml</a></p>