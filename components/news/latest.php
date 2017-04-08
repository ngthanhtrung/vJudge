<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<ul>
<?php
	if ($list = news::latestNews()) {
		foreach ($list as $item) {
?>
	<li><?php echo $item['title']; ?></li>
<?php
		}
	}
?>
</ul>
<div class="small"><a href="#news,do:latest">[<?php echo t('Details'); ?>]</a></div>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Latest News'),
		'content' => $content,
		'wrap' => true,
		'cache' => false
	);
	$template->setModule($mod);
?>