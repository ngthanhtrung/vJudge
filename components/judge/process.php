<?php
	defined('_SECURITY') or die("Access denied.");

	global $template;
	if (admin::adminPage()) {
		switch (varURL::getParam('do')) {
			case 'list':
				$str = $template->loadSingleModule('problem-manage', 'judge');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
		}
	}
	else {
		switch (varURL::getParam('do')) {
			case 'problem-details': case 'details':
				$str = $template->loadSingleModule('problem-details', 'judge');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
			case 'submit':
				$str = $template->loadSingleModule('submit', 'judge');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
			case 'latest-submission': case 'submission':
				$str = $template->runModule('latest-submission', 'judge');
				print $str;
			case 'ranks':
				$str = $template->loadSingleModule('ranks', 'judge');
				print "<return result=\"success\">" . htmlspecialchars($str) . "</return>";
				break;
			case 'problem-list': case 'list':
			default:
				$str = $template->runModule('problem-list', 'judge');
				print $str;
		}
	}
?>