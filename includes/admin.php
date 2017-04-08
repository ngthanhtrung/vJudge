<?php
	defined('_SECURITY') or die("Access denied.");
	
	class admin {
		static $isAdminPage;
		
		function adminPage() {
			if (isset(self::$isAdminPage)) return self::$isAdminPage;
			else {
				$queryStr = $_SERVER['QUERY_STRING'];
				if (($pos = strpos($queryStr, '&')) === false) $queryStr = trim($queryStr);
				else $queryStr = substr($queryStr, 0, $pos);
				self::$isAdminPage = ($queryStr == 'admin' ? true : false);
				return self::$isAdminPage;
			}
		}
		
		function isAdmin() {
			$currentUser = user::get();
			return ((!$currentUser || $currentUser['type'] != '1') ? false : true);
		}
		
		function menu() {
			return array(
				1 => array(
					'name' => 'Pages',
					'link' => '#page,do:list',
					'level' => 0,
				),
				2 => array(
					'name' => 'News',
					'link' => '#news,do:list',
					'level' => 0,
				),
				3 => array(
					'name' => 'Languages',
					'link' => '#language,do:list',
					'level' => 0,
				),
				4 => array('name' => '_space'),
				5 => array(
					'name' => 'Problems',
					'link' => '#judge,do:list',
					'level' => 0,
				)
			);
		}
	}
?>