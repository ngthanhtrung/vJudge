<?php
	defined('_SECURITY') or die("Access denied.");
	
	$list = page::getPageList();
	page::showPageList($list);
?>