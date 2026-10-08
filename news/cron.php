<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file The CRON interface to Gutuma
 * @modifications Cyril Maguire
 */
/* Gutama plugin package
 * @version 2.2.2
 * @date	01/06/2023
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
# ini_get('max_execution_time')
# Fix : Fatal error: Maximum execution time of 30 seconds exceeded
@set_time_limit(30+$batch_time_limit);
$max_exec = ini_get('max_execution_time');
$batch_time_limit = ($max_exec <= $batch_time_limit?($max_exec-3):$batch_time_limit);
# protect multiple instance on same time
$bip = realpath(GUTUMA_TEMP_DIR).'/.cronincourse';
if(is_file($bip) && (($batch_time_limit+filemtime($bip))>=(time()-$batch_time_limit)))
	exit('00');
@unlink($bip);
// Process outbox
$go = true;
foreach ($mailbox['outbox'] as $newsletter) {
	if($go) touch($bip);
	$send = $newsletter->get_send_progress();
	if($go) $newsletter->send_batch($mailer, $start_time);
	$sded = $go?$newsletter->get_send_progress():$send;
	// Counter
	if($go) $box++;
	$all = array(
		$all[0]+=($send[0]-$sded[0]),
		$all[1]+=$sded[0]
	);
	// Check batch time limit
	if ((time() - $start_time) > $batch_time_limit)
		break;
	$go = false;#
}
@unlink($bip);# end protect multiple instance on same time
echo $box;if($box)echo ';'.$all[0].';'.$all[1];#0 OR boxes;sended;rest all