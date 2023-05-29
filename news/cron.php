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
	die(t('Unable to access mailbox'));

// Create mailer
$mailer = new gu_mailer();
if (!$mailer->init())
	die(t('Unable to initialize mailer'));

$all = array(0,0);
$box = 0;
// Start timer
$start_time = time();
$batch_time_limit = (int)gu_config::get('batch_time_limit');
// Process outbox
foreach ($mailbox['outbox'] as $newsletter) {
	$send = $newsletter->get_send_progress();
	$newsletter->send_batch($mailer, $start_time);
	$sded = $newsletter->get_send_progress();
	// Counter
	$box++;
	$all = array(
		$all[0]+=($send[0]-$sded[0]),
		$all[1]+=$sded[0]
	);
	// Check batch time limit
	if ((time() - $start_time) > $batch_time_limit)
		break;
}
echo $box;if($box)echo ';'.$all[0].';'.$all[1];#boxes;sended;rest