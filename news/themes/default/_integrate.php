<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file included integration page
 * @modifications Cyril Maguire
 *
 * Gutama plugin package
 * @version 1.6
 * @date	01/10/2013
 * @author	Cyril MAGUIRE
*/
gu_theme_messages();
?>
<form id="gadgets_form" name="gadgets_form" method="post" action="">
	<div id="sectionheader" class="inline-form action-bar">
		<h2><?php echo t('Gadgets for other sites') . ($generate?' <i><sup><sub><i>'.t('Subscribe '.strtr($gadget_type,'_',' ')).'</sub></sup></i>':'');?></h2>
		<p id="sectionmenu" class="plx<?php echo str_replace('.','',PLX_VERSION) ?>"><sup><sub><?php echo t('Here you can generate gadgets for other websites so that people can easily find and subscribe to your newsletters.');?></sub></sup>
<?php if (!$generate) { ?>
		<input type="submit" id="gadget_generate" class="green" name="gadget_generate" value="<?php echo t('Next');?>" />
		</p><!-- sectionmenu -->
	</div><!-- sectionheader -->
	<!-- Because the gadgets are already inside a form, set formless to true, so that they don't get their own forms -->
	<script type="text/javascript">gu_gadgets_formless = true;</script>
	<div class="formfieldset gadget">
		<div class="formfield">
			<div class="formfieldlabel"><input type="radio" name="gadget_type" value="basic_link" checked="checked" /> <?php echo t('Subscribe basic link');?>:</div>
			<div class="formfieldcontrols"><script type="text/javascript">gu_gadgets_write_basic_link(<?php echo $example_list_id; ?>, '<?php echo t('Subscribe to my newsletter!');?>');</script></div>
		</div>
		<div class="formfield">
			<div class="formfieldlabel"><input type="radio" name="gadget_type" value="basic_form" /> <?php echo t('Subscribe basic form');?>:</div>
			<div class="formfieldcontrols"><script type="text/javascript">gu_gadgets_write_basic_form(<?php echo $example_list_id; ?>, '<?php echo t('Subscribe');?>', '<?php echo t('Your email');?>', 'bf_');</script></div>
		</div>
		<div class="formfield">
			<div class="formfieldlabel"><input type="radio" name="gadget_type" value="ajax_link" /> <?php echo t('Subscribe ajax link');?>:</div>
			<div class="formfieldcontrols"><script type="text/javascript">gu_gadgets_write_ajax_link(<?php echo $example_list_id; ?>, '<?php echo t('Subscribe to my newsletter!');?>');</script></div>
		</div>
		<div class="formfield">
			<div class="formfieldlabel"><input type="radio" name="gadget_type" value="ajax_form" /> <?php echo t('Subscribe ajax form');?>:</div>
			<div class="formfieldcontrols"><script type="text/javascript">gu_gadgets_write_ajax_form(<?php echo $example_list_id; ?>, '<?php echo t('Subscribe');?>', '<?php echo t('Your email');?>', 'af_');</script></div>
		</div>
	</div>
</form>
<?php } else {// generated ?>
		<input type="button" id="gadget_back" class="blue" name="gadget_back" value="<?php echo t('Back');?>" onclick="window.location=window.location" />
		<input type="submit" id="gadget_generate" class="green" name="gadget_generate" value="<?php echo t('Update');?>" /><input type="hidden" id="gadget_type" name="gadget_type" value="<?php echo $gadget_type; ?>" />
		</p><!-- sectionmenu -->
	</div><!-- sectionheader -->
	<div class="formfieldset">
<?php
	foreach ($gadget_params as $param){
		echo '<div class="formfield">';
		switch ($param) {
			case 'list':
				echo '  <div class="formfieldlabel">'.t('Address list').':</div>';
				echo '  <div class="formfieldcontrols">'.create_list_control('gadget_list', $gadget_list, $gadget_list_all).'</div>';
				break;
			case 'text':
				echo '  <div class="formfieldlabel">'.t('Link text').':</div>';
				echo '  <div class="formfieldcontrols">'.create_text_control('gadget_text', $gadget_text).'</div>';
				break;
			case 'btn_text':
				echo '  <div class="formfieldcomment">'.t('This can be left blank to create the gadget without a button').'</div>';
				echo '  <div class="formfieldlabel">'.t('Button text').':</div>';
				echo '  <div class="formfieldcontrols">'.create_text_control('gadget_btn_text', $gadget_btn_text).'</div>';
				break;
			case 'prefix':
				echo '  <div class="formfieldcomment">'.t('If two or more gadgets are going to be used on the same page, then they need to use unique prefixes').'</div>';
				echo '  <div class="formfieldlabel">'.t('Control prefix').':</div>';
				echo '  <div class="formfieldcontrols">'.create_text_control('gadget_prefix', $gadget_prefix).'</div>';
				break;
			case 'email_hint':
				echo '  <div class="formfieldlabel">'.t('Email hint').':</div>';
				echo '  <div class="formfieldcontrols">'.create_text_control('gadget_email_hint', $gadget_email_hint).'</div>';
				break;
		}
		echo '</div>';
	}
?>
	</div>
</form>
<h3><?php echo t('Preview');?></h3>
<div style="text-align: center; padding: 10px; background-color: #E0FFE0">
	<?php echo $script_write; ?>
</div>
<form id="code_form" name="code_form" method="post" action="">
	<h3><?php echo t('Script call (recommended)');?></h3>
	<p><?php echo t('The following should be added to the HEAD section of your webpage');?></p>
	<p><input name="gadget_import" type="text" class="textfield" style="width: 99%" id="gadget_import" onclick="this.focus(); this.select();" value="<?php echo htmlspecialchars($script_import); ?>" readonly="readonly" /></p>
	<p><?php echo t('This should be pasted to the place in your webpage where you want the gadget to appear');?></p>
	<p><input name="gadget_script" type="text" class="textfield" style="width: 99%" id="gadget_script" onclick="this.focus(); this.select();" value="<?php echo htmlspecialchars($script_write); ?>" readonly="readonly" /></p>
	<h3><?php echo t('Actual HTML (advanced)');?> </h3>
	<p><?php echo t('If you need to customize the gadget, you can copy and paste the actual HTML instead.');?></p>
	<?php if ($gadget_requires_import) { ?>
		<p><?php echo t('<strong>NOTE</strong> This gadget requires you to include <code>%</code> as above even if you use this HTML.',array('gadget.js.php'));?></p>
	<?php } ?>
	<p><textarea name="gadget_html" id="gadget_html" rows="6" cols="30" style="width: 100%;" onclick="this.focus(); this.select();" readonly="readonly"></textarea></p>
</form>
<script type="text/javascript">document.getElementById('gadget_html').value = <?php echo $script_create; ?></script>
<?php
}# Fi generate