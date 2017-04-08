<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<h3>Submit</h3>
<p>
	<iframe name="upload-status" id="upload-status" src="?com=judge&mod=upload" scrolling="no" frameborder="0"></iframe>
</p>
<form enctype="multipart/form-data" method="POST" action="?com=judge&mod=upload" target="upload-status">
	<p id="psubmit-code">
		<label for="psubmit-code-box"><?php echo t('Problem code'); ?></label>&nbsp;
		<input id="psubmit-code-box" type="text" name="code" size="20" value="<?php echo varURL::getParam('code'); ?>" />
	</p>
	<p id="psubmit-file">
		<label for="psubmit-file-box"><?php echo t('Choose a file'); ?></label>&nbsp;
		<input id="psubmit-file-box" type="file" name="uploaded_file" size="20" />
	</p>
	<p id="psubmit-pl">
		<label for="psubmit-pl-box"><?php echo t('Programming language'); ?></label>&nbsp;
		<select id="psubmit-pl-box" name="pl">
			<?php foreach (judge::plList() as $pl) { ?>
			<option value="<?php echo $pl['plid']; ?>"><?php echo $pl['name']; ?></option>
			<?php } ?>
		</select>
	</p>
	<input type="submit" value="<?php echo t('Upload'); ?>"/>
</form>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Submit a problem'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>