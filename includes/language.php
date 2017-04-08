<?php
	defined('_SECURITY') or die("Access denied.");
	
	class language {
		var $lang;
		var $langId;
		var $langCode;
		var $langList;
		
		function getDefaultLanguage($getAgain = false) {
			if (!$this->langId || $getAgain) {
				global $db;
				$id = (int)varDB::get('language');
				if (!$id || ($id == 0)) {
					$this->langId = 0;
					return 0;
				}
				$query = "SELECT * FROM #__languages"
				. "\n WHERE lid = %d";
				$db->query($query, $id);
				if ($db->noRow()) {
					$this->langId = 0;
					return 0;
				}
				else {
					$this->langId = $id;
					return $id;
				}
			}
			else return $this->langId;
		}
		
		function setDefaultLanguage($id) {
			varDB::set('language', $id);
			$this->langId = $this->getLanguage(true);
		}
		
		function getLanguage() {
			global $session;
			if (($lang = $session->get('current_language')) === false) {
				$lang = $this->getDefaultLanguage();
				$session->set('current_language', $lang);
			}
			return $lang;
		}
		
		function setLanguage($lang) {
			global $session;
			$session->set('current_language', $lang);
		}
		
		function getLanguageCode() {
			if (isset($this->langCode)) return $this->langCode;
			else {
				global $db;
				$query = "SELECT * FROM #__languages"
				. "\n WHERE lid = %d";
				$db->query($query, $this->getLanguage());
				if ($db->noRow()) return 'en';
				else {
					$val = $db->getRow();
					$this->langCode = substr($val['name'], 0, 2);
					return $this->langCode;
				}
			}
		}
		
		function getLanguageList() {
			if (isset($this->langList)) return $this->langList;
			else {
				global $db;
				$query = "SELECT * FROM #__languages";
				$db->query($query);
				$english = array('lid' => 0, 'name' => 'english', 'displayname' => 'English');
				if ($db->noRow()) $this->langList = array($english);
				else {
					$this->langList = $db->getList();
					array_unshift($this->langList, $english);
				}
				return $this->langList;
			}
		}
		
		function identChar($c) {
			return (ctype_alnum($c) || ($c == '_'));
		}
		
		function getChar($str, $pos) {
			if (isset($str[$pos])) return $str[$pos];
			else return '';
		}
		
		function format($str, $params = array()) {
			$re = '';
			$offset = 0;
			foreach ($params as $param) {
				do {
					$pos = strpos($str, '%', $offset);
					if ($pos === false) break;
					$re .= substr($str, $offset, $pos - $offset);
					if (!$this->identChar($this->getChar($str, $pos + 1))) {
						$re .= '%';
						$offset = $pos + 1;
						$cont = true;
					}
					else {
						$pos++;
						while ($this->identChar($this->getChar($str, $pos))) $pos++;
						$re .= $param;
						$offset = $pos;
						$cont = false;
					}
				} while ($cont);
				if ($pos === false) break;
			}
			$re .= substr($str, $offset);
			return $re;
		}
		
		// Plural and singular form
		function number($num, $singular, $plural) {
			switch ($num) {
				case 0: case 1:
					return t($singular, $num);
					break;
				default:
					return t($plural, $num);
			}
		}
		
		function locale($str) {
			global $db, $cache;
			$id = 0; // Default language ID
			if (!isset($this->lang)) {
				if (!$this->lang = $cache->get($id, 'language')) {
					$query = "SELECT content FROM #__locale_sources"
					. "\n WHERE LENGTH(content) < 75";
					$db->query($query);
					$this->lang = array();
					foreach ($db->getList() as $row)
						$this->lang[$row['content']] = true;
					$cache->save($this->lang, $id, 'language');
				}
			}
			
			if (!isset($this->lang[$str])) {
				$query = "SELECT content FROM #__locale_sources"
				. "\n WHERE content = %s";
				$db->query($query, $str);
				if ($db->noRow()) {
					$query = "INSERT INTO #__locale_sources"
					. "\n VALUES(NULL, %s)";
					$db->query($query, $str);
					$cache->remove($id, 'language');
				}
				$this->lang[$str] = true;
			}
			return $str;
		}
		
		function translate($str, $args) {
			global $db, $cache;
			
			$id = $this->getLanguage();
			if ($id == 0) $translated = $this->locale($str); // Default language
			else {
				// Else, translate it
				if (!isset($this->lang)) {
					if (!$this->lang = $cache->get($id, 'language')) {
						// Refresh database stored cache of translations for given language.
						// We only store short strings to improve performance and consume less memory.
						$query = "SELECT s.content AS scontent, t.content AS tcontent FROM #__locale_sources AS s"
						. "\n LEFT JOIN #__locale_targets AS t"
						. "\n ON t.source = s.lsid AND t.language = %d"
						. "\n WHERE LENGTH(s.content) < 75";
						$db->query($query, $id);
						$this->lang = array();
						foreach ($db->getList() as $row)
							$this->lang[$row['scontent']] = ($row['tcontent'] ? $row['tcontent'] : true);
						$cache->save($this->lang, $id, 'language');
					}
				}
				
				// If we have the translation cached, skip checking the database
				if (!isset($this->lang[$str])) {
					$query = "SELECT t.content AS content FROM #__locale_sources AS s"
					. "\n LEFT JOIN #__locale_targets AS t"
					. "\n ON t.source = s.lsid"
					. "\n WHERE t.language = %d AND s.content = %s";
					$db->query($query, $id, $str);
					if ($db->noRow()) {
						// We don't have the source string, cache this as untranslated.
						$query = "INSERT INTO #__locale_sources"
						. "\n VALUES(NULL, %s)";
						$db->query($query, $str);
						// Clear locale cache so this string can be added in a later request.
						$cache->remove($id, 'language');
						$this->lang[$str] = true;
					}
					else {
						$val = $db->getRow();
						$this->lang[$str] = $val['content'];
					}
				}
				$translated = ($this->lang[$str] === true ? $str : $this->lang[$str]);
			}
			return $this->format($translated, $args);
		}
	}
	
	$language = new language();
	
	function t() {
		global $language;
		$args = func_get_args();
		$str = array_shift($args);
		return $language->translate($str, $args);
	}
?>