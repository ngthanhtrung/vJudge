<?php
	defined('_SECURITY') or die("Access denied.");
	
	global $template;
	switch (varURL::getParam('do')) {
		case 'login':
			switch ($username = user::find(varPOST::get('username'), varPOST::get('password'))) {
				case USR_USERNAME_NOT_EXIST:
					print "<return do=\"login\" result=\"error\">Your username isn't exist.</return>";
					break;
				case USR_PASSWORD_INCORRECT:
					print "<return do=\"login\" result=\"error\">Your password isn't correct.</return>";
					break;
				default:
					user::login($username);
					buffer::start();
					file::inc('components/user/logout.php');
					$str = buffer::get();
					buffer::end();
					print "<return do=\"login\" result=\"success\">" . htmlspecialchars($str) . "</return>";
			}
			break;
		case 'logout':
			user::logout();
			print "<return do=\"logout\" result=\"success\"></return>";
			break;
		case 'forgotpass':
			$str = $template->loadSingleModule('forgotpass', 'user');
			print "<return do=\"forgotpass\" result=\"success\">" . htmlspecialchars($str) . "</return>";
			break;
		case 'register':
			$str = $template->loadSingleModule('register', 'user');
			print "<return do=\"register\" result=\"success\">" . htmlspecialchars($str) . "</return>";
			break;
		case 'submitregister':
			$str = $template->runModule('submitregister', 'user');
			print $str;
			break;
		case 'profile':
		default:
			$str = $template->loadSingleModule('profile', 'user');
			print "<return do=\"profile\" result=\"success\">" . htmlspecialchars($str) . "</return>";
			break;
	}
?>