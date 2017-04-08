<?php
	defined('_SECURITY') or die("Access denied.");
?>
var r = tryFindTag(xml, 'return');
var d = r.getAttribute('do');
switch (d) {
	case 'choose':
		var re = r.getAttribute('result');
		switch (re) {
			case 'error':
				showMessage(r.firstChild.data);
				break;
			case 'success':
				reload();
		}
		break;
	case 'list':
		var re = r.getAttribute('result');
		switch (re) {
			case 'error':
				showMessage(r.firstChild.data);
				break;
			case 'success':
				loadToMain(r.firstChild.data);
		}
}