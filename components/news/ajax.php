<?php
	defined('_SECURITY') or die("Access denied.");
?>
var r = tryFindTag(xml, 'return');
var re = r.getAttribute('result');
switch (re) {
	case 'error':
		showMessage(r.firstChild.data);
		break;
	case 'success':
		loadToMain(r.firstChild.data);
		break;
	case 'redirect':
		redirect('#news,do:list', '?admin&com=news&q=do:list');
		showMessage(r.firstChild.data);
		break;
	case 'table':
		loadToMain(tryFindTag(r, 'text').firstChild.data);
		createTable(tryFindTag(r, 'table'));
}