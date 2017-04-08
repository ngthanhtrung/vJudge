<?php
	defined('_SECURITY') or die("Access denied.");
	
	define('NOT_LOGGED_IN', 4);
	define('INCORRECT_PROBLEM_CODE', 5);
	define('COMPILE_FAILED', 6);
	
	class judge {
		function getProblem($pid, $id = null) {
			global $db, $language;
			if ($id === null) $id = $language->getLanguage();
			if ($id == 0) {
				$query = "SELECT * FROM #__problem_sources"
				. "\n WHERE psid = %d";
			} else {
				$query = "SELECT s.*, t.name AS tname, t.content AS tcontent"
				. "\n FROM #__problem_targets AS t"
				. "\n JOIN #__problem_sources AS s"
				. "\n ON t.source = s.psid"
				. "\n WHERE s.psid = %d";
			}
			$db->query($query, $pid);
			if ($db->noRow()) return false;
			else {
				$row = $db->getRow();
				if ($id != 0 && $row['tname']) {
					$row['name'] = $row['tname'];
					$row['content'] = $row['tcontent'];
				}
				return $row;
			}
		}
		
		function getProblemList($group, $id = null) {
			global $db, $language;
			if ($id == null) $id = $language->getLanguage();
			if ($id == 0) {
				$query = "SELECT * FROM #__problem_sources";
			} else {
				$query = "SELECT s.*, t.name AS tname, t.content AS tcontent"
				. "\n FROM #__problem_targets AS t"
				. "\n JOIN #__problem_sources AS s"
				. "\n ON t.source = s.psid";
			}
			if ($group) $query .= "\n WHERE pgroup = %d";
			$db->query($query, $group);
			if ($db->noRow()) return false;
			else {
				$list = $db->getList();
				foreach ($list as $i => $v) {
					if ($id != 0 && $list[$i]['tname']) {
						$list[$i]['name'] = $list[$i]['tname'];
						$list[$i]['content'] = $list[$i]['tcontent'];
					}
				}
				return $list;
			}
		}
		
		function showProblemList($list) {
			if (!$list)
				print "<return result=\"success\">"
				. htmlspecialchars(
					style::module('judge-problem-list', t('Problem list'), "<h4>" . t("There isn't any problem in this group now.") . "</h4>", false)
				)
				. "</return>";
			else {
				print "<return result=\"table\">";
				print "<text>" . htmlspecialchars("<h3>" . t('Problem list') . "</h3>") . "</text>";
				$table = array();
				$table[] = array(
					array('content' => t('Problem code')),
					array('content' => t('Problem name')),
					array('content' => t('Added date')),
					array('content' => t('Ratio solved')),
					array('content' => t('Point'))
				);
				foreach ($list as $item) {
					$table[] = array(
						array('content' => $item['code']),
						array('content' => '<a href="#judge,do:details,id:' . $item['psid'] . '">' . $item['name'] . '</a>'),
						array('content' => date('d/m/Y', strtotime($item['created']))),
						array('content' => $item['solved'] . '/' . $item['did']),
						array('content' => $item['mark'])
					);
				}
				print style::table($table, 'list', 'row', 'header');
				print "</return>";
			}
		}
		
		function get($code) {
			global $db;
			$query = "SELECT * FROM #__problem_sources WHERE code = %s";
			$db->query($query, $code);
			return ($db->noRow() ? false : $db->getRow());
		}
		
		function getPl($plid) {
			global $db;
			$query = "SELECT * FROM #__proglanguages"
			. "\n WHERE plid = %d";
			$db->query($query, $plid);
			return ($db->noRow() ? false : $db->getRow());
		}
		
		function plList() {
			global $db;
			$query = "SELECT * FROM #__proglanguages";
			$db->query($query, $code);
			return ($db->noRow() ? false : $db->getList());
		}
		
		function nextIdSubmission() {
			global $db;
			$query = "SELECT sid FROM #__submissions"
			. "\n ORDER BY sid DESC LIMIT 0, 1";
			$db->query($query);
			$res = $db->getRow();
			return (int)$res['sid'] + 1;
		}
		
		function getSubmission($sid) {
			global $db;
			$query = "SELECT * FROM #__submissions"
			. "\n WHERE sid = %d";
			$db->query($query, $sid);
			return ($db->noRow() ? false : $db->getRow());
		}
		
		function insertSubmission($sid, $pid) {
			global $db;
			$query = "INSERT INTO #__submissions"
			. "\n (sid, status, proglanguage, submitted, submittedby, problem)"
			. "\n VALUES (%d, %d, %d, NOW(), %d, %d)";
			$u = user::get();
			$db->query($query, $sid, -1, varPOST::get('pl'), $u['uid'], $pid);
		}
		
		function updateSubmission($sid, $status) {
			global $db;
			$query = "UPDATE #__submissions"
			. "\n SET status = %d"
			. "\n WHERE sid = %d";
			$db->query($query, $status, $sid);
		}
		
		function getSubmissionList() {
			global $db;
			$query = "SELECT s.*, u.displayname, p.code FROM #__submissions AS s"
			. "\n LEFT JOIN #__users AS u"
			. "\n ON s.submittedby = u.uid"
			. "\n LEFT JOIN #__problem_sources AS p"
			. "\n ON s.problem = p.psid"
			. "\n ORDER BY sid DESC";
			$db->query($query);
			return ($db->noRow() ? false : $db->getList());
		}
		
		function showSubmissionList($list) {
			if (!$list) {
				print "<return result=\"success\">"
				. htmlspecialchars(
					style::module('submission-list', t('Submission list'), "<h4>" . t("There isn't any submission.") . "</h4>", false)
				)
				. "</return>";
			}
			else {
				print "<return result=\"table\">";
				print "<text>" . htmlspecialchars("<h3>" . t('Submission list') . "</h3>") . "</text>";
				$table = array();
				$table[] = array(
					array('content' => t('ID')),
					array('content' => t('Problem')),
					array('content' => t('Submitted at')),
					array('content' => t('Submitted by')),
					array('content' => t('Mark'))
				);
				foreach ($list as $item) {
					if ((int)$item['status'] < 0) {
						switch ($item['status']) {
							case '-3': $status = '<span class="red">' . t('Error') . '</span>'; break;
							case '-2': $status = '<span class="blue">' . t('Compiling') . '</span>'; break;
							case '-1': $status = '<span class="gray">' . t('Pending') . '</span>'; break;
						}
					}
					else $status = '<span class="green">' . $item['status'] . '</span>';
					$table[] = array(
						array('content' => $item['sid'], 'class' => 'center'),
						array('content' => '<a href="#judge,do:details,id:' . $item['problem'] . '">' . $item['code'] . '</a>', 'class' => 'center'),
						array('content' => date('h:i:s A - d/m/Y', strtotime($item['submitted'])), 'class' => 'center'),
						array('content' => $item['displayname'], 'class' => 'center'),
						array('content' => $status, 'class' => 'center'),
					);
				}
				print style::table($table, 'list', 'row', 'header');
				print "</return>";
			}
		}
		
		function upload($sid) {
			if (!user::logged()) return NOT_LOGGED_IN;
			$p = self::get(varPOST::get('code'));
			if (!$p) return INCORRECT_PROBLEM_CODE;
			$targetPath = 'uploads/submissions/' . $sid . '.src';
			if (($re = file::uploadFile($targetPath, $p['maxsize'])) == UPLOAD_SUCCESS)
				self::insertSubmission($sid, $p['psid']);
			return $re;
		}
	}
?>