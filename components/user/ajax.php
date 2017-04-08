<?php
	defined('_SECURITY') or die("Access denied.");
?>
var r = tryFindTag(xml, 'return');
var d = r.getAttribute('do');
switch (d) {
	case 'login':
		var re = r.getAttribute('result');
		switch (re) {
			case 'error':
				showMessage(r.firstChild.data);
				break;
			case 'success':
				findId('user-log-logout').innerHTML = r.firstChild.data;
				log.changePage('logout');
		}
		break;
	case 'logout':
		var re = r.getAttribute('result');
		if (re == 'success') {
			if (admin) {
				window.location.assign('./');
				break;
			}
			log.changePage('login');
		}
		break;
	case 'profile': case 'forgotpass':
		var re = r.getAttribute('result');
		switch (re) {
			case 'error':
				showMessage(r.firstChild.data);
				break;
			case 'success':
				loadToMain(r.firstChild.data);
		}
		break;
	case 'register':
		var re = r.getAttribute('result');
		switch (re) {
			case 'error':
				showMessage(r.firstChild.data);
				break;
			case 'success':
				loadToMain(r.firstChild.data);
				Zapatec.Calendar.setup({
					firstDay          : 1,
					weekNumbers       : false,
					showOthers        : true,
					electric          : false,
					inputField        : "user-register-birthday-box",
					button            : "user-register-birthday-button",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%d/%m/%Y"
				});
		}
		break;
	case 'submitregister':
		var re = r.getAttribute('result');
		switch (re) {
			case 'error':
				showMessage(r.firstChild.data);
				break;
			case 'success':
				var user = r.getAttribute('user');
				var pass = r.getAttribute('pass');
				window.location.href = '#page,id:1';
				showMessage(r.firstChild.data);
				post('com=user&q=do:login', 'username=' + user + '&password=' + pass);
		}
}