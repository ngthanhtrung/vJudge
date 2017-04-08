<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<form id="user-log-form" onsubmit="javascript:login(); return false;">
	<p id="user-log-username">
		<label for="user-log-username-box"><?php echo t('Username'); ?></label><br />
		<input id="user-log-username-box" type="text" name="username" alt="<?php echo t('Username'); ?>" size="18" />
	</p>
	<p id="user-log-password">
		<label for="user-log-password-box"><?php echo t('Password'); ?></label><br />
		<input id="user-log-password-box" type="password" name="password" alt="<?php echo t('Password'); ?>" size="18" />
	</p>
	<p id="user-log-remember">
		<label for="user-log-remember-box"><?php echo t('Remember Me'); ?></label>&nbsp;
		<input id="user-log-remember-box" type="checkbox" name="remember" checked="yes" alt="<?php echo t('Remember Me'); ?>" />
	</p>
</form>
<button onclick="javascript:login();"><?php echo t('Login'); ?></button>
<ul>
	<li><a href="#user,do:forgotpass"><?php echo t('Forgot your password?'); ?></a></li>
	<li><a href="#user,do:register"><?php echo t('Create an account'); ?></a></li>
</ul>
<?php
	$pageLogin = buffer::get();
	buffer::end();
	
	buffer::start();
	file::inc('components/user/logout.php');
	$pageLogout = buffer::get();
	buffer::end();
	
	buffer::start();
?>
<script type="text/javascript">
	var log = new pageFile('user-log');
	log.addPage('login', <?php echo ajax::jsString($pageLogin); ?>);
	log.addPage('logout', <?php echo ajax::jsString($pageLogout); ?>);
	log.setPage('<?php echo (user::logged() ? 'logout' : 'login'); ?>');
	log.displayPage();
</script>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Login Form'),
		'content' => $content,
		'wrap' => true,
		'cache' => false
	);
	$template->setModule($mod);
?>