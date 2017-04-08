<?php
	defined('_SECURITY') or die("Access denied.");
	
	if (varPOST::get('promt') == 'yes') {
		global $db;
		
		$query = "DELETE FROM #__page_sources"
		. "\n WHERE psid = %d";
		$db->query($query, varPOST::get('id'));
		
		$query = "DELETE FROM #__page_targets"
		. "\n WHERE source = %d";
		$db->query($query, varPOST::get('id'));
		
		print "<return result=\"redirect\">Success.</return>";
	}
	else print "<return result=\"redirect\">Failed.</return>";
?>