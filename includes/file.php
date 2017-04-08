<?php
	defined('_SECURITY') or die("Access denied.");
	
	define('UPLOAD_SUCCESS', 1);
	define('FILE_TOO_LARGE', 2);
	define('UPLOAD_FAILED', 3);
	
	class file {
		function exist($filename) {
			return file_exists($filename);
		}
		
		function inc($filename) {
			if (self::exist($filename)) {
				include($filename);
				return true;
			}
			else return false;
		}
		
		function inc_once($filename) {
			if (self::exist($filename)) {
				include_once($filename);
				return true;
			}
			else return false;
		}
		
		function exec($filename) {
			buffer::start();
			self::inc($filename);
			$content = buffer::get();
			buffer::end();
			return $content;
		}
		
		function exec_once($filename) {
			buffer::start();
			self::inc_once($filename);
			$content = buffer::get();
			buffer::end();
			return $content;
		}
		
		function open($filename) {
			return file_get_contents($filename);
		}
		
		function save($filename, $content) {
			return file_put_contents($filename, $content);
		}
		
		function append($filename, $content) {
			return file_put_contents($filename, $content, FILE_APPEND);
		}
		
		function uploadFile($saveFileName, $maxFileSize) {
			if (!isset($_FILES['uploaded_file'])) return UPLOAD_FAILED;
			if ($_FILES['uploaded_file']['size'] > $maxFileSize) return FILE_TOO_LARGE;
			if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $saveFileName))
				return UPLOAD_SUCCESS;
			else return UPLOAD_FAILED;
		}
		
		function remove_dir($current_dir) {
			if ($dir = @opendir($current_dir)) {
				while (($f = readdir($dir)) !== false) {
					if ($f > '0' and filetype($current_dir . $f) == "file") {
						unlink($current_dir . $f);
					} elseif ($f > '0' and filetype($current_dir . $f) == "dir") {
						remove_dir($current_dir . $f . "\\");
					}
				}
				closedir($dir);
				rmdir($current_dir);
			}
		}
	}
?>