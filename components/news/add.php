<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<h3><?php echo t('Add news'); ?></h3>
<form id="news-add-form" onsubmit="return false;">
	<p><a href="#news,do:list"><?php echo t('Back to list'); ?></a></p>
	<p id="news-add-title">
		<label for="news-add-title-box"><?php echo t('Title'); ?></label><br />
		<input id="news-add-title-box" type="text" name="title" size="50" value="" />
	</p>
	<p>
		<label for="news-add-content-box"><?php echo t('Content'); ?></label><br />
		<textarea id="news-add-content-box" name="content" rows="15" cols="80" style="width: 90%">
		</textarea>
	</p>
	<input type="button" onclick="javascript:submitNewsAdd();" value="<?php echo t('Submit'); ?>" />
</form>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Add news'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>