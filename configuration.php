<?php
	defined('_SECURITY') or die("Access denied.");
	
	class config {
		// Site configuration
		const name = 'vJudge';
		const title = 'vJudge: Online Judge System';
		const titlespacer = ' | ';
		
		// Database connection
		const host = 'localhost';
		const user = 'root';
		const pass = '';
		const db = 'judge';
		const dbprefix = 'oj_';
		
		// Cache
		const cachedir = 'cache/';
	}
?>