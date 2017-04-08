<?php
	defined('_SECURITY') or die("Access denied.");
	
	class style {
		function module($name, $title, $content, $wrap) {	
			$file = template::getFolder() . 'styles/' . ($wrap ? 'wrap.php' : 'nowrap.php');
			$str = file::open($file);
			$search = array('%module-id', '%title-id', '%content-id', '%title', '%content');
			$replace = array($name, $name . '-title', $name . '-content', $title, $content);
			return str_replace($search, $replace, $str);
		}
		
		function table($content, $tclass = null, $rclass = null, $hclass = null) {
			$str = "<table" . ($tclass ? (" class=\"" . $tclass . "\"") : "") . ">";
			foreach ($content as $row) {
				if ($hclass) {
					$str .= "\n<row class=\"" . $hclass . "\">";
					$hclass = null;
				}
				else $str .= "\n<row" . ($rclass ? (" class=\"" . $rclass . "\"") : "") . ">";
				foreach ($row as $col) {
					$str .= "\n<col" . (isset($col['colspan']) ? (" colspan=\"" . $col['colspan'] . "\"") : "")
					. (isset($col['rowspan']) ? (" rowspan=\"" . $col['rowspan'] . "\"") : "")
					. (isset($col['class']) ? (" class=\"" . $col['class'] . "\"") : ""). ">";
					$str .= "\n" . htmlspecialchars($col['content']) . "\n</col>";
				}
				$str .= "\n</row>";
			}
			$str .= "\n</table>";
			return $str;
		}
	}
?>