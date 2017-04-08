<?php
	defined('_SECURITY') or die("Access denied.");
	
	$list = judge::getProblemList(varURL::getParam('group'));
	judge::showProblemList($list);
?>