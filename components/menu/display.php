<?php
	defined('_SECURITY') or die("Access denied.");
	
	global $template;
	$id = $template->getParam('id');
	if (($id == 'admin') || ($m = menu::get($id))) {
		if ($id == 'admin') {
			$mm = admin::menu();
			$content = menu::getContent($mm);
		}
		else $content = menu::getContent(menu::getItems($id));
		
		global $template;
		$mod = array(
			'title' => t((isset($m['name']) ? $m['name'] : 'Administrator')),
			'content' => $content,
			'wrap' => true,
			'cache' => false
		);
		$template->setModule($mod);
	}
?>