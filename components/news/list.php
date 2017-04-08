<?php
	defined('_SECURITY') or die("Access denied.");
	
	$list = news::getNewsList();
	news::showNewsList($list);
?>