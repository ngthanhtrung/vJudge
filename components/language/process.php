<?php
	defined('_SECURITY') or die("Access denied.");
	
	global $db, $template;
	
	if (varURL::getParam('do') == '') {
		$id = (int)varURL::getParam('id');
		if ($id != 0) {
			$query = "SELECT * FROM #__languages"
			. "\n WHERE lid = %d";
			$db->query($query, $id);
			if ($db->noRow()) {
				print "<return do=\"choose\" result=\"error\">" . t("The language you have choosen doesn't exist.") . "</return>";
				break;
			}
			else language::setLanguage($id);
		}
		else language::setLanguage(0);
		print "<return do=\"choose\" result=\"success\">" . $id . "</return>";
	}
	elseif (admin::adminPage()) {
		switch (varURL::getParam('do')) {
			case 'list': default:
				$str = $template->loadSingleModule('list', 'language');
				print "<return do=\"list\" result=\"success\">" . htmlspecialchars($str) . "</return>";
		}
	}
?>