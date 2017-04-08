<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<h3><?php echo t('Register an account'); ?></h3>
<form id="user-register-form">
	<p id="user-register-username">
		<label for="user-register-username-box"><?php echo t('Username'); ?></label><br />
		<input id="user-register-username-box" type="text" name="username" size="50" value="" />
	</p>
	<p id="user-register-displayname">
		<label for="user-register-displayname-box"><?php echo t('Display name'); ?></label><br />
		<input id="user-register-displayname-box" type="text" name="displayname" size="50" value="" />
	</p>
	<p id="user-register-password">
		<label for="user-register-password-box"><?php echo t('Password'); ?></label><br />
		<input id="user-register-password-box" type="password" name="password" size="50" value="" />
	</p>
	<p id="user-register-repassword">
		<label for="user-register-repassword-box"><?php echo t('Retype password'); ?></label><br />
		<input id="user-register-repassword-box" type="password" name="repassword" size="50" value="" />
	</p>
	<p id="user-register-email">
		<label for="user-register-email-box"><?php echo t('Email'); ?></label><br />
		<input id="user-register-email-box" type="text" name="email" size="50" value="" />
	</p>
	<p id="user-register-group">
		<label for="user-register-group-box"><?php echo t('Group'); ?></label><br />
		<select name="group" id="user-register-group-box">
		<?php foreach (user::groupList() as $group) { ?>
			<option value="<?php echo $group['gid']; ?>"><?php echo $group['name'] ?></option>
		<?php } ?>
		</select>
	</p>
	<p id="user-register-birthday">
		<label for="user-register-birthday-box"><?php echo t('Birthday'); ?></label><br />
		<input id="user-register-birthday-box" type="text" name="birthday" size="20" value="" />
		<input id="user-register-birthday-button" type="button" value="...">
	</p>
	<input type="button" onclick="javascript:submitUserAdd();" value="<?php echo t('Submit'); ?>" />
</form>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Registration'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>