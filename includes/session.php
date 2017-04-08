<?php
	defined('_SECURITY') or die("Access denied.");
	
	/**
	 * Manipulate the session
	 */
	class session {
		
		/**
		 * Create a session object
		 *
		 * @param $id
		 *   Session ID
		 */
		function session($id = null) {
			if (headers_sent()) {
				die("Failed to start the session, header sent to client.<br />\n<em>Please don't output before init the session.</em>");
			} else {
				if (isset($id)) session_id($id);
				session_start() or die("Failed to start the session.");
			}
		}
		
		/**
		 * Get value of a variable
		 *
		 * @param $name
		 *   Variable name
		 * @return
		 *   Value of the variable, return false if the variable wasn't be set;
		 */
		function get($name) {
			if (isset($_SESSION[$name])) return $_SESSION[$name];
			return false;
		}
		
		/**
		 * Set value for a variable
		 *
		 * @param $name
		 *   Variable name
		 * @param $val
		 *   Value will be set for the variable
		 */
		function set($name, $val) {
			$_SESSION[$name] = $val;
		}
		
		/**
		 * Delete a variable
		 *
		 * @param $name
		 *   Variable name
		 */
		function delete($name) {
			unset($_SESSION[$name]);
		}
		
		/**
		 * Free all variables of the session
		 */
		function free() {
			session_unset();
		}
		
		/**
		 * Destroy the session
		 */
		function destroy() {
			session_destroy();
		}
	}
?>