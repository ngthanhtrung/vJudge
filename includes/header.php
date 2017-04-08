<?php
	defined('_SECURITY') or die("Access denied.");
	
	class header {
		var $title = array();
		var $spacer = ' - ';
		var $tags = array();
		var $jsCode;
		
		function header($title, $spacer) {
			$this->title = array($title);
			$this->spacer = $spacer;
			$this->jsCode = array();
		}
		
		function addTitle($title) {
			$this->title[] = $title;
		}
		
		function addTag($content) {
			$this->tags[] = $content;
		}
		
		function addContent($content) {
			$tag = array(
				'name' => 'content',
				'content' => $content
			);
			$this->addTag($tag);
		}
		
		function addMeta($meta) {
			$tag = array(
				'name' => 'meta',
				'attribs' => $meta,
				'content' => false
			);
			$this->addTag($tag);
		}
		
		function addCSS($link, $media = null) {
			$tag = array(
				'name' => 'link',
				'attribs' => array(
					'rel' => 'stylesheet',
					'href' => $link,
					'type' => 'text/css'
				),
				'content' => false
			);
			if ($media)	$tag['attribs']['media'] = $media;
			$this->addTag($tag);
		}
		
		function addJS($link) {
			$tag = array(
				'name' => 'script',
				'attribs' => array(
					'src' => $link,
					'type' => 'text/javascript'
				),
				'content' => true
			);
			$this->addTag($tag);
		}
		
		function addJSCode($code) {
			$this->jsCode[] = $code;
		}
		
		function getTitle() {
			$title = t(array_shift($this->title));
			foreach ($this->title as $str)
				$title .= $this->spacer . $str;
			return $title;
		}
		
		function getHeader() {
			$return = "<title>" . $this->getTitle() . "</title>\n";
			foreach ($this->tags as $tag) {
				if ($tag['name'] == 'content')
					$str = $tag['content'];
				else {
					$str = "<" . $tag['name'];
					foreach ($tag['attribs'] as $name => $value)
						$str .= " " . $name . "=\"" . $value . "\"";
					if ($tag['content'])
						$str .= ">" . ($tag['content'] === true ? "" : "\n" . $tag['content'] . "\n") . "</" . $tag['name'] . ">";
					else $str .= " />";
				}
				$return .= $str . "\n";
			}
			if (count($this->jsCode) >= 1) {
				$str = "<script type=\"text/javascript\">";
				foreach ($this->jsCode as $code)
					$str .= "\n" . $code;
				$str .= "\n</script>";
				$return .= $str;
			}
			return $return;
		}
	}
?>