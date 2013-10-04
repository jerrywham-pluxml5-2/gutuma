<?php 
/**
 * Fichier d'aide pour gutuma
 *
 * @version 1.3
 * @date	29/10/2012
 * @author	Cyril MAGUIRE
 **/
if(!defined('PLX_ROOT')) exit; ?>

<h2>Aide</h2>
<h3>Fichier d&#039;aide du plugin Gutuma, le gestionnaire de Newsletters</h3>

<h3>Installation</h3>
<p>Le plugin est constitué de deux dossiers principaux :</p>
<ol>
<li>le dossier <code>gutuma</code> qui doit être placé dans le dossier des plugins de Pluxml</li>
<li>le dossier <code>news</code> contenu dans le dossier gutuma</li>
</ol>
<p style="color:red;">Les dossiers et fichiers suivants doivent être éditables :</p>
<ul>
<li><code>news/lists/</code></li>
<li><code>news/temp/</code></li>
<li><code>news/inc/config.php</code> (ce fichier sera créé lors de l'installation. Assurez-vous de modifier les droits du dossier <code>news/inc</code> pour permettre sa création, et de les modifier à nouveau une fois le fichier <code>config.php</code> créé)</li>
</ul>
<p>L'adresse admin doit être renseignée.</p>
<p>&nbsp;</p>
<h3 style="color:red;">IMPORTANT POUR LA MIGRATION DE LA V1.1 A LA V1.2 AFIN DE NE PAS PERDRE LES ANCIENNES LISTES DE DIFFUSION</h3>
<ol>
<li>Remplacer l'ancien dossier gutuma par le nouveau dans le dossier plugins</li>
<li>Copier tout le contenu de l'ancien dossier /news/lists/ (celui à la racine de pluxml) dans le dossier /plugins/gutuma/news/lists</li>
<li>Supprimer le dossier /news (celui à la racine de pluxml)</li>
</ol>
<h3>Activation</h3>
<p>Pensez &agrave; activer le plugin dans le gestionnaire des plugins de Pluxml.<br/>
Seuls les administrateurs et les gestionnaires peuvent utiliser le plugin.</p>
<p>Les administrateurs ont accès aux réglages, tandis que les gestionnaires ne peuvent que gérer les news et les listes d'envoi.</p>
<p>&nbsp;</p>
<p>Le module doit être installé <span style="color:red;">par l'administrateur principal (le numéro 001) du site</span> (<strong>Important !</strong>).</p>
<p>&nbsp;</p>
<p>Une fois le plugin activé, un lien "Gutuma" apparait dans la sidebar du panneau d'administration. Un clic sur ce lien dirige vers une liste d'utilisateurs susceptibles de pouvoir utiliser le plugin (les administrateurs et les gestionnaires). Par défaut, ils sont tous désactivés , sauf le premier utilisateur.</p>
<p>&nbsp;</p>
<p>Pour poursuivre l'installation, ce dernier doit cliquer sur le lien <code> Installer le module</code>.</p>
<p>Il est alors dirigé vers la page d'installation du module de news. Les informations affichées sont modifiables. Un clic sur le bouton <code>installer</code> termine l'installation.</p>
<p>L'utilisateur principal doit alors activer son compte en se connectant avec les mêmes identifiants que ceux utilisés pour la connexion au panneau d'administration de Pluxml.</p>
<p>&nbsp;</p>
<p>Une fois ces quelques manipulations effectuées, les autres utilisateurs peuvent être activés à leur tour.</p>
<h3>Utilisation</h3>
<p>Les administrateurs peuvent rédiger des news, gérer les utilisateurs, les listes de diffusion et les réglages du plugins. Les gestionnaires ne peuvent que rédiger les news et gérer les listes de diffusion.
</p>
<p>Après avoir cliqué sur <code>Ecrire une newsletter</code>, une liste d'options s'affichent dans la sidebar, sous le menu Gutuma. Ce sont des liens vers les différentes fonctionnalités du plugin.</p>
<p>&nbsp;</p>
<h3>Configuration</h3>
<p>La configuration se fait en mode administrateur uniquement. C'est l'option <code>Réglages</code> du menu de la sidebar qui permet de faire les modifications. Les champs sont suffisamment explicites et ne seront pas détaillés ici.</p>
<p>Un panneau d'informations sur l'installation et les paramètres système est également disponible.</p>
<h3>Gadgets</h3>
<p>Pour permettre aux lecteurs du site de s'abonner aux newsletters, des codes javascripts sont à placer sur la partie publique du site, dans le code source. Différentes possibilités sont offertes : affichage de liens, de bouton ou de champ de formulaire.</p>
<p>Pour mettre en page ces codes d'abonnement, des index css sont disponibles :  #suscribe-link pour les liens basiques et ajax, et #gu_subscribe_form pour les formulaires.</p>