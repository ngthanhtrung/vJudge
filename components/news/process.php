<?php
	defined('_SECURITY') or die("Access denied.");
	
	global $template;
	if (admin::adminPage()) {
		switch (varURL::getParam('do')) {
			case 'edit':
				$str = $template->loadSingleModule('edit', 'news');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
				break;
			case 'submitedit':
				$str = $template->runModule('submitedit', 'news');
				print $str;
				break;
			case 'add':
				$str = $template->loadSingleModule('add', 'news');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
				break;
			case 'submitadd':
				$str = $template->runModule('submitadd', 'news');
				print $str;
				break;
			case 'delete':
				$str = $template->runModule('delete', 'news');
				print $str;
				break;
			case 'list':
			default:
				$str = $template->runModule('list', 'news');
				print $str;
		}
	}
	else {
		switch (varURL::getParam('do')) {
			case 'latest':
			default:
				$str = $template->loadSingleModule('latest-details', 'news');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
		}
	}
?>