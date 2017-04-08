<?php
	defined('_SECURITY') or die("Access denied.");
	
	class buffer {
		function start() {
			ob_start();
		}
		
		function get() {
			return ob_get_contents();
		}
		
		function clean() {
			ob_clean();
		}
		
		function end($clean = true) {
			if ($clean) ob_end_clean();
			else ob_end();
		}
	}
?>