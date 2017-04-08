<?php
	defined('_SECURITY') or die("Access denied.");
	
	$item = judge::getProblem(varURL::getParam('id'));
	buffer::start();
	if (!$item) {
?>
<h4><?php echo t("Can't find this problem."); ?></h4>
	<?php } else { ?>
<h3><?php echo $item['name']; ?></h3>
<p><a href="#judge,do:submit,code:<?php echo $item['code'];?>">Answer this problem</a></p>
<div><?php echo $item['content']; ?></div>
<?php
	}
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Problem details'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>