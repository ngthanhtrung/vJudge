<?php
	defined('_SECURITY') or die("Access denied.");
	
	class varURL {
		static $params;
		
		function get($name) {
			return (isset($_GET[$name]) ? $_GET[$name] : false);
		}
		
		function getParam($name) {
			if (!isset(self::$params)) self::$params = explode(',', self::get('q'));
			foreach (self::$params as $param) {
				$pos = strpos($param, ':');
				if (trim(substr($param, 0, $pos)) == $name) return substr($param, $pos + 1);
			}
			return false;
		}
	}
	
	class varPOST {
		function get($name) {
			return (isset($_POST[$name]) ? $_POST[$name] : false);
		}
	}
	
	class varDB {
		function get($name) {
			global $db;
			$query = "SELECT * FROM #__vars"
			. "\n WHERE LOWER(name) = %s";
			$db->query($query, strtolower($name));
			if ($db->noRow())
				return false;
			else {
				$row = $db->getRow();
				return $row['value'];
			}
		}
		
		function set($name, $value) {
			global $db;
			$query = "REPLACE INTO #__vars"
			. "\n VALUES(%s, %s)";
			$db->query($query, strtolower($name), $value);
		}
	}
?>