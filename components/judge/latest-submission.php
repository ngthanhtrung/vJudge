<?php
	defined('_SECURITY') or die("Access denied.");
	
	$list = judge::getSubmissionList();
	judge::showSubmissionList($list);
?>