<?php
	defined('_SECURITY') or die("Access denied.");
	
	global $db;
	$val = user::get();
	$query = "INSERT INTO #__page_sources"
	. "\n (title, content, created, createdby)"
	. "\n VALUES (%s, %s, NOW(), %d)";
	$db->query($query, varPOST::get('title'), varPOST::get('content'), $val['uid']);
	print "<return result=\"redirect\">Success.</return>";
?>