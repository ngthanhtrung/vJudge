<?php
	defined('_SECURITY') or die("Access denied.");
	
	class page {
		static $c;
		
		function get($id, $lid = null) {
			global $db, $language;
			if (!isset($c)) $c = array();
			if (!isset($c[$id])) {
				if ($lid === null) $lid = $language->getLanguage();
				if ($lid == 0)
					$query = "SELECT * FROM #__page_sources"
					. "\n WHERE psid = %d";
				else
					$query = "SELECT s.title as stitle, s.content as scontent, s.created, s.createdby, t.*"
					. "\n FROM #__page_sources AS s"
					. "\n LEFT JOIN #__page_targets AS t"
					. "\n ON t.source = s.psid"
					. "\n WHERE s.psid = %d";
				$db->query($query, $id);
				if ($db->noRow()) $c[$id] = false;
				else {
					$c[$id] = $db->getRow();
					if ($lid != 0 && !$c[$id]['title']) {
						$c[$id]['title'] = $c[$id]['stitle'];
						$c[$id]['content'] = $c[$id]['scontent'];
					}
				}
			}
			return $c[$id];
		}
		
		function getPageList() {
			global $db;
			$query = "SELECT p.*, u.displayname FROM #__page_sources AS p"
			. "\n LEFT JOIN #__users AS u"
			. "\n ON p.createdby = u.uid"
			. "\n ORDER BY p.psid";
			$db->query($query);
			return ($db->noRow() ? false : $db->getList());
		}
		
		function showPageList($list) {
			if (!$list) {
				print "<return result=\"success\">"
				. htmlspecialchars(
					style::module('page-list', t('Page list'), "<h4>" . t("There isn't any page.") . "</h4>", false)
				)
				. "</return>";
			}
			else {
				print "<return result=\"table\">";
				print "<text>" . htmlspecialchars(
					"<h3>" . t('Page list') . "</h3>"
					. "\n<div class=\"add\"><a href=\"#page,do:add\">" . t('Add a new page') . "</a></div><br />"
				) . "</text>";
				$table = array();
				$table[] = array(
					array('content' => t('ID')),
					array('content' => t('Title')),
					array('content' => t('Written at')),
					array('content' => t('Author')),
					array('content' => t('Edit')),
					array('content' => t('Delete')),
				);
				foreach ($list as $item) {
					$table[] = array(
						array('content' => $item['psid'], 'class' => 'center'),
						array('content' => '<a href="#page,do:edit,id:' . $item['psid'] . '">' . $item['title'] . '</a>'),
						array('content' => date('d/m/Y', strtotime($item['created'])), 'class' => 'center'),
						array('content' => $item['displayname'], 'class' => 'center'),
						array('content' => '<a href="#page,do:edit,id:' . $item['psid'] . '"><img src="images/edit.png" /></a>', 'class' => 'center'),
						array('content' => '<a href="javascript:submitPageDelete(' . $item['psid'] . ');"><img src="images/delete.png" /></a>', 'class' => 'center'),
					);
				}
				print style::table($table, 'list', 'row', 'header');
				print "</return>";
			}
		}
	}
?>