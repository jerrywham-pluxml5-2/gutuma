			</div>
			<div id="footer"><a href="<?php echo GUTUMA_URL; ?>">Gutuma</a> is released under the GPL | <a href="http://ijuru.com/gutuma/support.php">Help</a> | &copy; <a href="http://rowan.ijuru.com">Rowan</a> </div>
			<?php
			if (gu_is_debugging() && isset($_SERVER['GU_DEBUG_MSGS'])) {
				echo '<div id="debugmsg"><p style="text-align: center"><b>Debug messages</b></p><hr />';
				foreach ($_SERVER['GU_DEBUG_MSGS'] as $msg)
					echo $msg.'<hr />';
				
				echo '</div>';	
			}
			?>		
		</div>
	</body>
</html>