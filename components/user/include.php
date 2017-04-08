<?php
	defined('_SECURITY') or die("Access denied.");
	
	define('USR_USERNAME_NOT_EXIST', -1);
	define('USR_PASSWORD_INCORRECT', -2);
	
	define('REG_OK', 0);
	define('REG_DUPLICATE_USER', 1);
	define('REG_PASSWORD_RETYPE_NOT_MATCH',2);
	
	class user {
		function find($username, $password) {
			global $db;
			$query = "SELECT * FROM #__users"
			. "\n WHERE LOWER(username) = %s";
			$db->query($query, strtolower($username));
			if ($db->noRow())
				return USR_USERNAME_NOT_EXIST;
			else {
				$row = $db->getRow();
				if ($row['password'] != $password)
					return USR_PASSWORD_INCORRECT;
				else return $row['username'];
			}
		}
		
		function get() {
			if ($u = self::logged()) {
				global $db;
				$query = "SELECT * FROM #__users"
				. "\n WHERE username = %s";
				$db->query($query, $u);
				return ($db->noRow() ? false : $db->getRow());
			}
			return false;
		}
		
		function getUser($uid) {
			global $db;
			$query = "SELECT * FROM #__users"
			. "\n WHERE uid = %s";
			$db->query($query, $uid);
			return ($db->noRow() ? false : $db->getRow());
		}
		
		function login($username) {
			global $session;
			$session->set('USR_USERNAME', $username);
		}
		
		function logged() {
			global $session;
			return $session->get('USR_USERNAME');
		}
		
		function logout() {
			global $session;
			$session->delete('USR_USERNAME');
		}
		
		function groupList() {
			global $db;
			$query = "SELECT * FROM #__user_groups";
			$db->query($query);
			return ($db->noRow() ? false : $db->getList());
		}
		
		function register($username, $displayname, $password, $retype, $email, $group, $birthday) {
			global $db;
			if ($password <> $retype)
				return REG_PASSWORD_RETYPE_NOT_MATCH;
			$query = "SELECT * FROM #__users"
			. "\n WHERE (username = %s) OR (email = %s)";
			$db->query($query, $username, $email);
			if ($db->noRow()) {
				$query = "INSERT INTO #__users"
				. "\n (username, displayname, password, email, type, ugroup, birthday, regdate)"
				. "\n VALUES (%s, %s, %s, %s, '2', %d, %s, CURDATE())";
				$db->query($query, $username, $displayname, $password, $email, $group, $birthday);
				return REG_OK;
			}
			else return REG_DUPLICATE_USER;	
		}
	}
?>