<?php
/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Javascript functions for generating integration gadgets - Default theme main stylesheet
 * @modifications Cyril Maguire
 * 
 * Gutama plugin package
 * @version 1.8.4
 * @date	01/09/2017
 * @author	Cyril MAGUIRE, Thomas I.
*/
include_once str_replace('js/gadgets.js.php','',__FILE__).'/inc/gutuma.php';//_if gutuma symlinked folder ::: origin is : include_once '../inc/gutuma.php';
gu_init(FALSE, FALSE, FALSE);// Initialize Gutuma without validation or housekeeping
if (!is_get_var('noajax')){
?>/**
 * This file is a server-side merge of tw-sack.js and gadgets.js.php
 *
 * ------------------------------ tw-sack.js -------------------------------
 */

<?php echo file_get_contents('tw-sack.js'); ?>
/**
 * ----------------------------- gadgets.js.php ------------------------------
 */
<?php } ?>
var gu_gadgets_formless = false;
var gu_gadgets_subcribe_url = "<?php echo absolute_url($gdgt.'subscribe.php') ?>";
var gu_gadgets_ajax_url = "<?php echo absolute_url($gdgt.'ajax.php'); ?>";
var gu_gadgets_ajax_proxy = "";
/**
 * The callback for errors from the AJAX interface
 */
function gu_ajax_on_error(msg){
	msg = msg.replace(/<(?:.|\s)*?>/g, "");
	alert(msg);
}
/**
 * Because AJAX doesn't support cross-domain requests, this function allows a proxy
 * to be set as the destination for AJAX requests
 * @param url The url of the proxy script which must be on this domain
 */
function gu_gadgets_set_ajax_proxy(url){
	gu_gadgets_ajax_proxy = url;
}
/**
 * Creates a basic subscribe link to the subscribe page
 * @param list_id The ID of a list (optional)
 * @param text The text of the link
 * @return The gadget HTML
 */
function gu_gadgets_create_basic_link(list_id, text){
	var sub_url = gu_gadgets_subcribe_url + ((list_id > 0) ? ("?list=" + list_id) : '');
	return '<a href="' + sub_url + '" class="subscribe-link" id="suscribe-link">' + text.replace(/&quot;/g,'"') + '</a>';
}
/**
 * Creates a basic subscribe form which redirects to the subscribe page
 * @param list_id The ID of a list (optional)
 * @param btn_text The submit button label (optional)
 * @param prefix The prefix applied to all IDs of elements
 * @return The gadget HTML
 */
function gu_gadgets_create_basic_form(list_id, btn_text, prefix){
	var html = '';
	if (!gu_gadgets_formless)
		html += '<form name="' + prefix + 'subscribe_form" id="' + prefix + 'subscribe_form" method="post" action="' + gu_gadgets_subcribe_url + '">';
	html += '<input name="' + prefix + 'subscribe_address" id="' + prefix + 'subscribe_address" type="text" />';
	html += '<input name="' + prefix + 'subscribe_list" id="' + prefix + 'subscribe_list" type="hidden" value="' + list_id + '" />';
	if (btn_text != '')
		html += '<input name="' + prefix + 'subscribe_submit" id="' + prefix + 'subscribe_submit" type="submit" value="' + btn_text.replace(/"/g,"&quot;") + '"/>';
	else
		html += '<input name="' + prefix + 'subscribe_submit" id="' + prefix + 'subscribe_submit" type="hidden" value="" />';
	if (!gu_gadgets_formless)
		html += '</form>';
	return html;
}
/**
 * Creates a link which uses AJAX to submit a subscription
 * @param list_id The ID of a list
 * @param text The text of the link
 * @return The gadget HTML
 */
function gu_gadgets_create_ajax_link(list_id, text){
	if (list_id == '' || list_id == 0)
		return '<?php echo t('This gadget requires a valid list');?>';
	return '<a href="javascript:gu_gadgets_submit_ajax_link(\'' + list_id + '\')" class="subscribe-link" id="suscribe-link">' + text.replace(/&quot;/g,'"') + '</a>';
}
/**
 * Creates a subscribe form which uses an inline AJAX submission
 * @param list_id The ID of a list
 * @param btn_text The submit button label (optional)
 * @param prefix The prefix applied to all IDs of elements
 * @return The gadget HTML
 */
function gu_gadgets_create_ajax_form(list_id, btn_text, email_hint, prefix){
	if (list_id == '' || list_id == 0)
		return '<?php echo t('This gadget requires a valid list');?>';
	var html = '';
	if (!gu_gadgets_formless)
		html += '<form name="' + prefix + 'subscribe_form" id="' + prefix + 'subscribe_form" method="get" action="" onsubmit="gu_gadgets_submit_ajax_form(this, \'' + prefix + '\'); return false;">';
	html += '<input name="' + prefix + 'subscribe_address" id="' + prefix + 'subscribe_address" type="text" />';
	html += '<input name="' + prefix + 'subscribe_list" id="' + prefix + 'subscribe_list" type="hidden" value="' + list_id + '" />';
	if (btn_text != '')
		html += '<input name="' + prefix + 'subscribe_submit" id="' + prefix + 'subscribe_submit" type="submit" value="' + btn_text + '"/>';
	if (!gu_gadgets_formless)
		html += '</form>';
	if (email_hint != '')
		html += '<script type="text\/javascript">gu_gadgets_textfield_hint(document.getElementById("' + prefix + 'subscribe_address"), "' + email_hint.replace(/&quot;/g,'\\"') + '");<\/script>';	//SyntaxError: unterminated string literal
	return html;
}
/**
 * Shortcut functions for outputting gadgets
 */
function gu_gadgets_write_basic_link(list_id, text){
	document.write(gu_gadgets_create_basic_link(list_id, text));
}
function gu_gadgets_write_basic_form(list_id, btn_text, prefix){
	document.write(gu_gadgets_create_basic_form(list_id, btn_text, prefix));
}

function gu_gadgets_write_ajax_link(list_id, text){
	document.write(gu_gadgets_create_ajax_link(list_id, text));
}
function gu_gadgets_write_ajax_form(list_id, btn_text, email_hint, prefix){
	document.write(gu_gadgets_create_ajax_form(list_id, btn_text, email_hint, prefix));
}

/**
 * Adds a hint value to the specified text field, i.e., when the box doesn't have focus it displays
 * the hint value, but when the user gives it focus the hint dissappears.
 * @param input The input to add the hint to
 * @param hint The hint value
 */
function gu_gadgets_textfield_hint(input, hint){// Add custom hint property
	input.hint = hint;
	input.onfocus = function(){
		if (this.value == this.hint)
			this.value = '';
		this.style.fontStyle='normal';
		this.style.color='#000';
	}
	input.onblur = function(){
		if (this.value == ''){
			this.style.fontStyle='italic';
			this.style.color='#BBB';
			this.value = this.hint;
		}
	}
	input.style.fontStyle='italic';
	input.style.color='#BBB';
	input.value = hint;
}
/**
 * Gets the url for AJAX requests, using the proxy if it has been specified
 * @return The AJAX request URL
 */
function gu_gadgets_get_ajax_url(){
	if (gu_gadgets_ajax_proxy != null && gu_gadgets_ajax_proxy != "")
		return gu_gadgets_ajax_proxy + "?url=" + escape(gu_gadgets_ajax_url);
	return gu_gadgets_ajax_url;
}
/**
 * Submits an AJAX subscribe request for the given list. The email address is requested in a client-side Javascript prompt.
 * @param list_id The ID of the list.
 */
function gu_gadgets_submit_ajax_link(list_id){
	var address = prompt("<?php echo t('Please enter your email address');?>");
	if (address == null)
		return;
	var url = gu_gadgets_get_ajax_url();
	var mysack = new sack(url);
	mysack.execute = 1;
	mysack.method = "POST";
	mysack.setVar("action", "subscribe");
	mysack.setVar("address", address);
	mysack.setVar("list", list_id);
	mysack.onError = function(){
		alert("<?php echo t('An error occured whilst requesting subscription');?>");
	};
	mysack.runAJAX();
}
/**
 * Submits an AJAX subscribe request using params from the specified form
 */
function gu_gadgets_submit_ajax_form(form, prefix){
	var txt_address = eval("form." + prefix + "subscribe_address");
	var txt_list = eval("form." + prefix + "subscribe_list");
	var btn_submit = eval("form." + prefix + "subscribe_submit");
	var address = (("hint" in txt_address) && (txt_address.value == txt_address.hint)) ? "" : txt_address.value;
	var list = txt_list.value;
	if (address == "" || list == "")
		return;
	var old_hint = txt_address.hint;
	gu_gadgets_textfield_hint(txt_address, "<?php echo t('Requesting...');?>");
	txt_address.disabled = true;
	if (btn_submit)
		btn_submit.disabled = true;
	var url = gu_gadgets_get_ajax_url();
	var mysack = new sack(url);
	mysack.execute = 1;
	mysack.method = "POST";
	mysack.setVar("action", "subscribe");
	mysack.setVar("address", address);
	mysack.setVar("list", list);
	mysack.onCompletion = function(){
		if (old_hint != '')
			gu_gadgets_textfield_hint(txt_address, old_hint);
		else
			txt_address.value = '';
		txt_address.disabled = false;
		if (btn_submit)
			btn_submit.disabled = false;
	};
	mysack.onError = function(){
		alert("<?php echo t('An error occured whilst requesting subscription');?>");
		txt_address.disabled = false;
		if (btn_submit)
			btn_submit.disabled = false;
	};
	mysack.runAJAX();
}