<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
	$c = page::get(varURL::getParam('id'));
	if (!$c) {
?>
<h4><?php echo t("Can't find this page."); ?></h4>
	<?php } else { ?>
<h3><?php echo $c['title']; ?></h3>
<?php echo $c['content'] ?>
<?php
	}
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => (isset($c['title']) ? $c['title'] : 'Content'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>