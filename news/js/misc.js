/************************************************************************
 * @project Gutuma Newsletter Managment
 * @author Rowan Seymour
 * @copyright This source is distributed under the GPL
 * @file Miscellaneous Javascript functions
 */
var gu_status_message = "";
var gu_error_message = "";
/**
 * Checks the validity of the specified email address
 * @param e The email address to check
 * @return TRUE if address is valid, else FALSE
 */
function gu_check_email(e){
	ok = "1234567890qwertyuiop[]asdfghjklzxcvbnm.@-_QWERTYUIOPASDFGHJKLZXCVBNM&";
	for (i = 0; i < e.length; i++){
		if (ok.indexOf(e.charAt(i)) < 0){
			return (false);
		}
	}
	if (document.images){
		re = /(@.*@)|(\.\.)|(^\.)|(^@)|(@$)|(\.$)|(@\.)/;
		re_two = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		if (!e.match(re) && e.match(re_two)){
			return (-1);
		}
	}
}
/**
 * Checks to see if the specified key event is a numeric key
 * @param evt The key event to check
 * @return TRUE if key was numeric, else FALSE
 */
function gu_is_numeric_key(evt){
	evt = ( evt ) ? evt : window.event;
	var charCode = ( evt.which ) ? evt.which : evt.keyCode;
	return (charCode <= 31 || (charCode >= 48 && charCode <= 57));
}
/**
 * Removes leading and trailing whitespace from a string
 * @param s The string to trim
 */
function gu_trim(s){
	return (s ? '' + s : '').replace(/^\s*|\s*$/g, '');
}
/**
 * Sets the status message
 * @param msg The message
 */
function gu_success(msg){
	gu_status_message = msg;
}
/**
 * Sets the error message
 * @param msg The message
 */
function gu_error(msg){
	gu_error_message = msg;
}
/**
 * Called when user clicks the "more" or "less" links on an error message
 */
function gu_messages_toggle_error_extra(){
	if (gu_element_get_display("errorextra") == "none"){
		gu_element_set_display("errorextra", "block");
		gu_element_set_display("errormore", "none");
		gu_element_set_display("errorless", "block");
	}
	else{
		gu_element_set_display("errorextra", "none");
		gu_element_set_display("errormore", "block");
		gu_element_set_display("errorless", "none");
	}
}
/**
 * Clears the current status and error messages
 */
function gu_messages_clear(){
	gu_error_message = "";
	gu_status_message = "";
}
function gu_add_slashes(str){
	return str.replace(/\"/g, "\\\"");
}
/**
 * Displays the current status and error messages
 * @param delay The delay in milliseconds
 */
function gu_messages_display(delay){ 
	if (gu_status_message != ""){
		setTimeout('gu_element_set_inner_html("statusmsg", "' + gu_add_slashes(gu_status_message) + '")', delay);
		if (gu_element_get_display("statusmsg") == "none"){
			setTimeout('gu_element_fade_in("statusmsg", 1000, "block")', delay);
		}
	}
	else
		setTimeout('gu_element_fade_out("statusmsg", 1000)', delay);
	if (gu_error_message != ""){
		if (document.getElementById("errormore")){
			gu_element_set_display("errormore", "none");
			gu_element_set_display("errorextra", "none");
		}
		setTimeout('gu_element_set_inner_html("errormsg", "' + gu_add_slashes(gu_error_message) + '")', delay);
		if (gu_element_get_display("errormsg") == "none")
			setTimeout('gu_element_fade_in("errormsg", 1000, "block")', delay);
	}
	else
		setTimeout('gu_element_fade_out("errormsg", 1000)', delay);
}
/**
 * Gets the display value of the specified element
 * @param id The id of the element
 * @return The CSS display value
 */
function gu_element_get_display(id){
	return document.getElementById(id).style.display;
}
/**
 * Sets the display value of the specified element
 * @param id The id of the element to modify
 * @param display The CSS display value
 */
function gu_element_set_display(id, display){
	document.getElementById(id).style.display = display;
}
/**
 * Sets the inner HTML of the specified element
 * @param id The id of the element to modify
 * @param display The HTML
 */
function gu_element_set_inner_html(id, html){
	document.getElementById(id).innerHTML = html;
}
/**
 * Sets the background color of the specified element
 * @param id The id of the element to modify
 * @param color The CSS color value
 */
function gu_element_set_background(id, color){
	document.getElementById(id).style.backgroundColor = color;
}
/**
 * Sets the opacity of the specified element
 * @param id The id of the element to modify
 * @param opacity The opacity 0...100
 */
function gu_element_set_opacity(id, opacity){
	var element = document.getElementById(id);
	element.style.MozOpacity = (opacity / 100);// For Mozilla
	element.style.filter = "alpha(opacity=" + opacity + ")";// For IE
	element.style.opacity = (opacity / 100);// For others
}
/**
 * Fades out an element
 * @param id The element to fade out
 * @param duration The animation duration in milliseconds
 * @param The CSS display value for the element
 */
function gu_element_fade_in(id, duration, display){
	setMsge(id);
	gu_element_set_opacity(id, 0);// Start element 100% transparent
	gu_element_set_display(id, display);// Display element
	gu_element_fade(id, duration, 0, 100);// Animate fade in
}
/**
 * Fades out an element
 * @param id The element to fade out
 * @param duration The animation duration in milliseconds
 */
function gu_element_fade_out(id, duration){
	gu_element_fade(id, duration, 100, 0);// Animate fade out
	setTimeout("gu_element_set_display('" + id + "', 'none');", duration);// Hide element completely at end of fade
}
/**
 * Fades in or out an element
 * @param id The element to fade out
 * @param duration The animation duration in milliseconds
 * @param start The starting opacity
 * @param end The ending opacity
 */
function gu_element_fade(id, duration, start, end){
	var fadeTime = Math.round(duration / 100);
	var i = 0;
	if (start < end){// Fade in
		for (j = start; j <= end; j++, i++){
			setTimeout("gu_element_set_opacity('" + id + "'," + j + ")", i * fadeTime);
		}
	}
	else if (start > end){// Fade out
		for (j = start; j >= end; j--, i++){
			setTimeout("gu_element_set_opacity('" + id + "'," + j + ")", i * fadeTime);
		}
	}
}
function setMsge(id){
	if(document.getElementById(id)){
		setTimeout("gu_element_fade_out('" + id + "', 1618)",3236);//fadeOut(id);
	}
}