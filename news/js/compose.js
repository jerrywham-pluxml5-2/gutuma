oto = to = too = noo = null;//timeouts autosave global vars
timer = document.getElementById('gu_timer').value;//global var
const gu_next_save = document.getElementById('gu_next_save');//global var
const gu_is_modified = document.getElementById('is_modified');//global var
const gu_auto_save_msg = document.getElementById('gu_auto_save_msg');//global var
document.getElementById('gu_auto_save_field').style.display='';
document.getElementById('gu_auto_save_overlay').style.display='';
function gu_presend_check(){
	if (document.send_form.msg_recips.value == ''){
		alert(l_gu_presend_check_alert);
		return false;
	}
	if (document.send_form.msg_subject.value == '')
		return confirm(l_gu_presend_check_confirm);
	return true;
}
function gu_add_recipient(){
	let txtRecips = document.send_form.msg_recips;
	let lstRecips = document.send_form.send_lists;
	txtRecips.value = gu_trim(txtRecips.value);
	if(txtRecips.value.search(lstRecips.value) < 0){//-1 only one time
		if (txtRecips.value != '')
			txtRecips.value += '; ';
		txtRecips.value += lstRecips.value;
		gu_set_modified(true);
	}
}
function gu_cancel_unsaved_warning(){
	is_post_back = true;
}
function gu_set_modified(modified){
	gu_is_modified.value = (modified ? 1 : 0);
}
function gu_auto_save_renew(){
	let time = document.getElementById('gu_timer').value;
	let is_modified = gu_is_modified.value;
	if(gu_timeoutHandle)
		window.clearTimeout(gu_timeoutHandle);
	if(gu_plx_domain && gu_plx_domain == gu_now_domain){//Same domain ok
		gu_plx_domain = 0;
		gu_timeoutHandle = setTimeout(gu_auto_save, time);
	}else{window.location = gu_ajax_url_auto_save_renew;}//alert('disconnected');
}
function gu_auto_save(){
	if(!to){
		to = document.querySelector('#gu_auto_save_overlay > span > b');
		oto = to.textContent;//save original time out
	}
	let cm = document.querySelector('[aria-label="HTML source code"]');//aria-label="HTML source code"//codemirror
	if (document.querySelector('#mceu_1[aria-disabled="false"]'))//tiny save btn method : yep
		gu_set_modified(true);
	let time = document.getElementById('gu_timer').value;
	let is_modified = gu_is_modified.value;
	gu_next_save_set();//countdown
	if(time<2){//stop all
		if(gu_timeoutHandle)
			window.clearTimeout(gu_timeoutHandle);
		return;
	}

	if (is_modified && gu_is_edited && gu_is_previewed){
		setTimeout(function(){
			if(window.location.hash=='#gu_auto_save_overlay'){
				document.getElementById('gu_no_save_btn').style.display='none';//hide cancel btn
// The first element that matches (or null if none do):
				let cm = document.querySelector('[aria-label="HTML source code"]');//codemirror div iframe is opened
				if(cm){//codemirror
					setTimeout(function(){//classic is timeout
						document.getElementById('save_submit').click();
					},1111);
					cm.querySelectorAll('button')[1].click();//save codemirror code with ok button to tiny
				}
				else{//classic
					document.getElementById('save_submit').click();
				}
				try{localStorage.setItem('gu_auto_save', new Date().toLocaleString());}catch(e){}
			}else if(time>0){
				if(gu_timeoutHandle)
					window.clearTimeout(gu_timeoutHandle);
				gu_timeoutHandle = setTimeout(gu_auto_save, time);
				gu_next_save_set();
				clearInterval(too);
				to.textContent = oto;//re set to original time out
			}
		},5000);
		if(time>1){
			window.location.hash='gu_auto_save_overlay';//active overlay;
			too = setInterval(function(){// timer before save
				if(to.textContent > 0){to.textContent = to.textContent - 1;}
				else{clearInterval(too);}
			},1000);
		}
	}else{
		if(time>1){
			gu_is_edited = gu_is_previewed = !0;
			if(!gu_plx_domain){
				let mysack = new sack(gu_ajax_url_auto_save);
				mysack.execute = 1;
				mysack.method = 'GET';
				mysack.setVar('gu_plx_domain', 'ajax');
				mysack.runResponse = function(){try{eval(this.response);}catch(e){console.log('Error gu_auto_save',e);}};
				mysack.onError = function(){ gu_error(l_gu_error_ajax); gu_messages_display(0); };
				mysack.onCompletion = function(){
					if(mysack.xmlhttp.status == 200){gu_auto_save_renew();}
				};
				mysack.runAJAX();
			}else{
				if(gu_timeoutHandle)
					window.clearTimeout(gu_timeoutHandle);
				if(gu_plx_domain && gu_plx_domain == gu_now_domain){//Good domain
					gu_plx_domain = 0;
					gu_timeoutHandle = setTimeout(gu_auto_save, time);
				}
			}
		}
	}
}
function gu_auto_save_is_active(){
	let time = document.getElementById('gu_timer').value;
	document.getElementById('autosave').value = time;
	if(time<2){//stop all
		if(gu_timeoutHandle)
			window.clearTimeout(gu_timeoutHandle);
	}
	else if((!timer && time) || timer != time){
		if(gu_timeoutHandle)
			window.clearTimeout(gu_timeoutHandle);
		gu_timeoutHandle = setTimeout(gu_auto_save, time);
	}
	timer = time;
	gu_next_save_set();//countdown
}
function gu_next_save_set(){
	if(noo)clearInterval(noo);
	gu_next_save.textContent = (timer<2?'--':timer/1000);
	if(timer>1){
		noo = setInterval(function(){
			if(gu_next_save.textContent > 0){gu_next_save.textContent = gu_next_save.textContent - 1;}
			else{clearInterval(noo);}
		},1000);
	}
}
window.onbeforeunload = function (ev){
	let is_modified = gu_is_modified.value;
	if (!is_post_back && is_modified)
		return l_gu_post_back;
}
document.addEventListener('DOMContentLoaded', //window.onload = function(){}
	function(e){
		let auto_save_msg = '';
		gu_auto_save();
		gu_next_save_set();
		try {auto_save_msg = localStorage.getItem('gu_auto_save');localStorage.removeItem('gu_auto_save');}catch(e){}
		if(auto_save_msg) gu_auto_save_msg.innerHTML = ' <b class="gu_auto_save_msg">'+l_gu_auto_save+' <i>'+auto_save_msg+'</i></b>';
	}
);
document.getElementById('gu_timer').addEventListener('change', gu_auto_save_is_active);