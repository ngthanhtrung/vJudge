<?php
	define('_SECURITY', true);
	
	// Configuration
	require_once('configuration.php');
	
	// Create the database object
	require_once('includes/database.php');
	$db = new database(config::host, config::user, config::pass, config::db, config::dbprefix);
	
	// Create the session object
	require_once('includes/session.php');
	$session = new session();
	
	// Control all global variables
	require_once('includes/variable.php');
	
	// File control
	require_once('includes/file.php');
	
	// Cache
	require_once('includes/cache.php');
	$options = array('cacheDir' => config::cachedir);
	$cache = new cache($options);
	
	// XML parser
	require_once('includes/xml.php');
	$xml = new xml();
	
	// Buffering
	require_once('includes/buffer.php');
	
	// Language
	require_once('includes/language.php');
	
	// Header
	require_once('includes/header.php');
	$header = new header(config::title, config::titlespacer);
	$meta = array (
		'http-equiv' => 'Content-Type',
		'content' => 'text/html; charset=utf-8'
	);
	$header->addMeta($meta);
	
	// Template
	require_once('includes/template.php');
	require_once('includes/style.php');
	$template = new template();
	
	$tag = array(
		'name' => 'link',
		'attribs' => array(
			'href' => $template->getFolder() . 'images/favicon.ico',
			'rel' => 'shortcut icon',
			'type' => 'image/x-icon'
		),
		'content' => false
	);
	$header->addTag($tag);
	
	// JS
	$header->addJS('includes/js/general.js');
	$header->addJS('includes/js/main.js');
	
	// AJAX
	require_once('includes/ajax.php');
	ajax::construct();
	
	require_once('includes/admin.php');
	if (varURL::get('com') && varURL::get('mod')) {
		$template->loadComponents(false);
		$template->blankPage();
	}
	else {
		// Editor
		$header->addJS('includes/js/tiny_mce/tiny_mce.js');
		$code = file::exec('includes/js/editor-init.php');
		$header->addJSCode($code);
		$header->addJS('includes/js/editor.js');
		
		// Calendar
		$header->addJS('includes/js/calendar/src/calendar.js');
		$header->addJS('includes/js/calendar/lang/calendar-en.js');
		$header->addCSS('includes/js/calendar/themes/aqua.css');
		$header->addCSS('includes/js/calendar/themes/layouts/small.css');
		
		$template->loadComponents();
		if (admin::adminPage()) {
			if (admin::isAdmin()) {
				if (!varURL::get('com')) {
					$header->addTitle(t('Administrator'));
					$template->loadModules(true);
					$template->displayPage();
				}
				else $template->replyRequest();
			}
			else header("Location: ./");
		}
		else {
			if (!varURL::get('com')) {
				$template->loadModules();
				$template->displayPage();
			}
			else $template->replyRequest();
		}
	}
?>