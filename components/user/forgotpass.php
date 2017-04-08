<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<h3>Forgot your password?</h3>
<div>This feature isn't supported right now.</div>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Forgot your password?'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>