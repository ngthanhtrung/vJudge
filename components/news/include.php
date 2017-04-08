<?php
	defined('_SECURITY') or die("Access denied.");
	
	class news {
		static $n;
		
		function latestNews($id = null) {
			global $db, $language;
			if ($id === null) $id = $language->getLanguage();
			if ($id == 0)
				$query = "SELECT * FROM #__news_sources"
				. "\n ORDER BY created DESC"
				. "\n LIMIT 0, 5";
			else
				$query = "SELECT s.title as stitle, s.content as scontent, s.created, s.createdby, t.*"
				. "\n FROM #__news_sources AS s"
				. "\n LEFT JOIN #__news_targets AS t"
				. "\n ON t.source = s.nsid"
				. "\n ORDER BY s.created DESC"
				. "\n LIMIT 0, 5";
			$db->query($query);
			if ($db->noRow()) return false;
			else {
				$list = $db->getList();
				foreach ($list as $i => $v) {
					if ($id != 0 && !$list[$i]['title']) {
						$list[$i]['title'] = $list[$i]['stitle'];
						$list[$i]['content'] = $list[$i]['scontent'];
					}
				}
				return $list;
			}
		}
		
		function get($id, $lid = null) {
			global $db, $language;
			if (!isset($c)) $c = array();
			if (!isset($c[$id])) {
				if ($lid == null) $lid = $language->getLanguage();
				if ($lid == 0)
					$query = "SELECT * FROM #__news_sources"
					. "\n WHERE nsid = %d";
				else
					$query = "SELECT s.title as stitle, s.content as scontent, s.created, s.createdby, t.*"
					. "\n FROM #__news_sources AS s"
					. "\n LEFT JOIN #__news_targets AS t"
					. "\n ON t.source = s.nsid"
					. "\n WHERE s.nsid = %d";
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
		
		function getNewsList() {
			global $db;
			$query = "SELECT n.*, u.displayname FROM #__news_sources AS n"
			. "\n LEFT JOIN #__users AS u"
			. "\n ON n.createdby = u.uid"
			. "\n ORDER BY n.nsid";
			$db->query($query);
			return ($db->noRow() ? false : $db->getList());
		}
		
		function showNewsList($list) {
			if (!$list) {
				print "<return result=\"success\">"
				. htmlspecialchars(
					style::module('news-list', t('News list'), "<h4>" . t("There isn't any news.") . "</h4>", false)
				)
				. "</return>";
			}
			else {
				print "<return result=\"table\">";
				print "<text>" . htmlspecialchars(
					"<h3>" . t('News list') . "</h3>"
					. "\n<div class=\"add\"><a href=\"#news,do:add\">" . t('Add a new news') . "</a></div><br />"
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
						array('content' => $item['nsid'], 'class' => 'center'),
						array('content' => '<a href="#news,do:edit,id:' . $item['nsid'] . '">' . $item['title'] . '</a>'),
						array('content' => date('d/m/Y', strtotime($item['created'])), 'class' => 'center'),
						array('content' => $item['displayname'], 'class' => 'center'),
						array('content' => '<a href="#news,do:edit,id:' . $item['nsid'] . '"><img src="images/edit.png" /></a>', 'class' => 'center'),
						array('content' => '<a href="javascript:submitNewsDelete(' . $item['nsid'] . ');"><img src="images/delete.png" /></a>', 'class' => 'center'),
					);
				}
				print style::table($table, 'list', 'row', 'header');
				print "</return>";
			}
		}
	}
?>