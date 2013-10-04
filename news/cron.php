<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The CRON interface to Gutuma
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/


include_once 'inc/gutuma.php';
include_once 'inc/newsletter.php';
include_once 'inc/mailer.php';

// Initialize Gutuma without validation or redirection
gu_init(FALSE, FALSE);

// Get all newsletters in the outbox
$mailbox = gu_newsletter::get_mailbox();
if ($mailbox == FALSE || !isset($mailbox['outbox']))
	die(utf8_decode(t('Unable to access mailbox')));
	
// Create mailer
$mailer = new gu_mailer();
if (!$mailer->init())	
	die(utf8_decode(t('Unable to initialize mailer')));
	
// Start timer
$start_time = time();
	
// Process outbox
foreach ($mailbox['outbox'] as $newsletter) {
	$newsletter->send_batch($mailer, $start_time);
	
	// Check batch time limit
	if ((time() - $start_time) > (int)gu_config::get('batch_time_limit'))
		break;
}

?>