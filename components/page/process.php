<?php
	defined('_SECURITY') or die("Access denied.");
	
	global $template;
	if (admin::adminPage()) {
		switch (varURL::getParam('do')) {
			case 'edit':
				$str = $template->loadSingleModule('edit', 'page');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
				break;
			case 'submitedit':
				$str = $template->runModule('submitedit', 'page');
				print $str;
				break;
			case 'add':
				$str = $template->loadSingleModule('add', 'page');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
				break;
			case 'submitadd':
				$str = $template->runModule('submitadd', 'page');
				print $str;
				break;
			case 'delete':
				$str = $template->runModule('delete', 'page');
				print $str;
				break;
			case 'list': default:
				$str = $template->runModule('list', 'page');
				print $str;
		}
	}
	else {
		switch (varURL::getParam('do')) {
			case 'display': default:
				if (!page::get(varURL::getParam('id')))
					print "<return result=\"error\">The content doesn't exist.</return>";
				else {
					$str = $template->loadSingleModule('display', 'page');
					print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
				}
		}
	}
?>