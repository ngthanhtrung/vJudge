<?php
	defined('_SECURITY') or die("Access denied.");
	
	class ajax {
		function construct() {
			global $header;
			$header->addJS('includes/js/ajax.js');
		}
		
		function register($code) {
			global $template, $header;
			$function = "function _" . $template->getComponentName() . "_reply(xml) {\n" . $code . "}";
			$header->addJSCode($function);
		}
		
		function jsString($str) {
			$arr = explode("\n", $str);
			foreach ($arr as $i => $v) $arr[$i] = str_replace("'", "\'", trim($arr[$i]));
			return "'" . implode('', $arr) . "'";
		}
	}
?>