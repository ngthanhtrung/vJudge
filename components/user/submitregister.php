<?php
	defined('_SECURITY') or die("Access denied.");
	
	$re = user::register(
		varPOST::get('username'),
		varPOST::get('displayname'),
		varPOST::get('password'),
		varPOST::get('repassword'),
		varPOST::get('email'),
		varPOST::get('group'),
		varPOST::get('birthday')
	);
	switch ($re) {
		case REG_DUPLICATE_USER:
			print "<return do=\"submitregister\" result=\"error\">" . t('This username or email exists.') . "</return>";
			break;
		case REG_PASSWORD_RETYPE_NOT_MATCH:
			print "<return do=\"submitregister\" result=\"error\">" . t('This retype password isn\'t match.') . "</return>";
			break;
		case REG_OK:
			print "<return do=\"submitregister\" result=\"success\" user=\"" . varPOST::get('username')
			. "\" pass=\"" . varPOST::get('password') . "\">" . t('Success') . "</return>";
	}
?>