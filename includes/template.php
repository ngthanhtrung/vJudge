<?php
	defined('_SECURITY') or die("Access denied.");
	
	class template {
		var $place;
		var $componentName;
		var $module;
		var $title;
		var $params;
		
		function template() {
			$this->place = array();
		}
		
		/* Template functions */
		function getDefaultTemplate() {
			$val = varDB::get('template');
			return ($val ? $val : 'default');
		}
		
		function setDefaultTemplate($str) {
			varDB::set('template', $str);
			$this->setCurrentTemplate($str);
		}
		
		function getCurrentTemplate() {
			global $session;
			$val = $session->get('current_template');
			if (!$val) {
				$val = $this->getDefaultTemplate();
				$session->set('current_template', $val);
			}
			return $val;
		}
		
		function setCurrentTemplate($str) {
			global $session;
			$session->set('current_template', $str);
		}
		
		function getFolder() {
			return 'templates/' . $this->getCurrentTemplate() . '/';
		}
		
		/* Set which component or module is loading */
		function setComponentName($com) {
			$this->componentName = $com;
		}
		
		function getComponentName() {
			return $this->componentName;
		}
		
		/* Load components and modules */
		function appendPlace($name, $content) {
			if (isset($this->place[$name]))
				$this->place[$name] .= $content;
			else $this->place[$name] = $content;
		}
		
		function loadComponents($js = true) {
			global $db, $header;
			$query = "SELECT * FROM #__components"
			. "\n WHERE enabled = '1'";
			$db->query($query);
			foreach ($db->getList() as $row) {
				$this->setComponentName($row['name']);
				buffer::start();
				file::inc_once("components/" . $row['name'] . "/include.php");
				if ($js) {
					buffer::clean();
					if (file::inc_once("components/" . $row['name'] . "/ajax.php")) {
						$code = buffer::get();
						ajax::register($code);
					}
					buffer::clean();
					if (file::inc_once("components/" . $row['name'] . "/javascript.php")) {
						$code = buffer::get();
						$header->addJSCode($code);
					}
				}
				buffer::end();
			}
		}
		
		function setModule($mod) {
			$this->module = $mod;
		}
		
		function getModule() {
			return $this->module;
		}
		
		function processParam($paramStr) {
			$this->params = array();
			$paramStr = trim($paramStr);
			if ($paramStr != '' && $paramStr != 'yes') {
				$list = explode(',', $paramStr);
				foreach ($list as $item) {
					if (($pos = strpos($item, '=')) !== false) {
						$i = trim(substr($item, 0, $pos));
						$v = trim(substr($item, $pos + 1));
						$this->params[$i] = $v;
					}
				}
			}
		}
		
		function getParam($name) {
			return (isset($this->params[$name]) ? $this->params[$name] : false);
		}
		
		function loadSingleModule($module, $component, $param = '') {
			$this->setComponentName($component);
			$this->setModule(false);
			$this->processParam($param);
			buffer::start();
			file::inc("components/" . $component . "/" . $module . ".php");
			buffer::end();
			$mod = $this->getModule();
			// Cache?
			return ($mod ? style::module($component . '-' . $module , $mod['title'], $mod['content'], $mod['wrap']) : '');
		}
		
		function runModule($module, $component, $param = '') {
			$this->setComponentName($component);
			$this->setModule(false);
			$this->processParam($param);
			return file::exec("components/" . $component . "/" . $module . ".php");
		}
		
		function loadModules($admin = false) {
			global $db;
			$query = "SELECT m.*, p.name AS pname, c.name AS cname FROM #__autoload_modules AS m"
			. "\n JOIN #__placeholders AS p"
			. "\n ON m.location = p.pid"
			. "\n JOIN #__components AS c"
			. "\n ON m.component = c.cid"
			. "\n WHERE c.enabled = '1'"
			. ($admin ? "\n AND admin != 'no'" : "\n AND normal != 'no'")
			. "\n ORDER BY m.position";
			$db->query($query);
			foreach ($db->getList() as $row) {
				$str = $this->loadSingleModule($row['name'], $row['cname'], ($admin ? $row['admin'] : $row['normal']));
				$this->appendPlace($row['pname'], $str);
			}
		}
		
		/* Output */
		function getContent($name) {
			return (isset($this->place[$name]) ? $this->place[$name] : false);
		}
		
		function replyRequest() {
			header("Content-type: application/xml; charset=utf-8"); // Only accept XML
			$this->setComponentName(varURL::get('com'));
			buffer::start();
			file::inc_once("components/" . varURL::get('com') . "/process.php");
			$content = buffer::get();
			buffer::end();
			print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
			. "\n<reply>"
			. "\n<for>" . varURL::get('com') . "</for>"
			. "\n$content"
			. "</reply>";
		}
		
		function blankPage() {
			header("Content-type: text/html; charset=utf-8");
			file::inc_once($this->getFolder() . 'blank.php');
		}
		
		function displayPage() {
			header("Content-type: text/html; charset=utf-8");
			file::inc_once($this->getFolder() . 'index.php');
		}
	}
?>