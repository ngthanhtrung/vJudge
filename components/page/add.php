<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<h3><?php echo t('Add page'); ?></h3>
<form id="page-add-form" onsubmit="return false;">
	<p><a href="#page,do:list"><?php echo t('Back to list'); ?></a></p>
	<p id="page-add-title">
		<label for="page-add-title-box"><?php echo t('Title'); ?></label><br />
		<input id="page-add-title-box" type="text" name="title" size="50" value="" />
	</p>
	<p>
		<label for="page-add-content-box"><?php echo t('Content'); ?></label><br />
		<textarea id="page-add-content-box" name="content" rows="15" cols="80" style="width: 90%">
		</textarea>
	</p>
	<input type="button" onclick="javascript:submitPageAdd();" value="<?php echo t('Submit'); ?>" />
</form>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Add page'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>