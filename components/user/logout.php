<?php
	defined('_SECURITY') or die("Access denied.");
?>
<p id="user-log-greeting"><?php echo (($username = user::logged()) ? t('Hi') . ' ' . $username . ',' : '&nbsp'); ?></p>
<p>
	<a href="#user,do:profile"><?php echo t('My profile'); ?></a>
<?php
	if (admin::isAdmin()) {
		if (admin::adminPage()) {
?>
	<br /><a href="./"><?php echo t('Homepage'); ?></a>
		<?php } else { ?>
	<br /><a href="?admin"><?php echo t('Administrator'); ?></a>
<?php
		}
	}
?>
</p>
<button onclick="javascript:get('com=user&q=do:logout')"><?php echo t('Logout'); ?></button>