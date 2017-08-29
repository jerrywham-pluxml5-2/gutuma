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
if(!defined('PLX_ROOT')) exit;
?>
		</section>
</main>
<?php
if(gu_session_is_valid()){//only admin valid user
	$plxAdmin = @plxAdmin::getInstance();
	eval($plxAdmin->plxPlugins->callHook('AdminFootEndBody')); ?>
<!--
<script type="text/javascript">
	//setMsg();
	mediasManager.construct({
		windowName : "<?php echo L_MEDIAS_TITLE ?>",
		racine:	"<?php echo plxUtils::getRacine() ?>",
		urlManager: "core/admin/medias.php"
	});
</script>
-->
<?php
	if (gu_is_debugging() && isset($_SERVER['GU_DEBUG_MSGS'])){
		echo '<div id="debugmsg"><p style="text-align: center"><b>Debug messages</b></p><hr />';
		foreach ($_SERVER['GU_DEBUG_MSGS'] as $msg)
			echo $msg.'<hr />';
		echo '</div>';
	}
}
?>
<script type="text/javascript">setMsge('errormsg');setMsge('statusmsg');</script>
	</body>
</html>