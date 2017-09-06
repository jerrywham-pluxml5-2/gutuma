<?php 
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included footer page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
?>
		</div>
		<div id="footer"><a href="<?php echo GUTUMA_URL; ?>" onclick="window.open(this.href);return false;">Gutuma</a> <?php echo t('is released under the GPL');?> | &copy; Rowan | <?php echo isset($_SESSION['user'])?'<a href="https://web.archive.org/web/20151029055359/http://www.gutuma.com/support.php">'.t('Help').'</a> | <a href="../../../core/admin/'.($_SESSION['profil'] == PROFIL_ADMIN?'plugin.php?p=gutuma':'').'">Admin PluXml</a>':'<a href="'.PLX_GROOT.'">'.t('Back home').'</a>'; ?></div>
<?php
			if (gu_is_debugging() && isset($_SERVER['GU_DEBUG_MSGS'])) {
				echo '<div id="debugmsg"><p style="text-align: center"><b>Debug messages</b></p><hr />';
				foreach ($_SERVER['GU_DEBUG_MSGS'] as $msg)
					echo $msg.'<hr />';
				echo '</div>';
			}
?>
		</div>
<script type="text/javascript">setMsge('errormsg');setMsge('statusmsg');</script>
	</body>
</html>