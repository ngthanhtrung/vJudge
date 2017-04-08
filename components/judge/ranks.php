<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<h3>Ranks</h3>
<div>This feature isn't supported right now.</div>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Ranks'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>