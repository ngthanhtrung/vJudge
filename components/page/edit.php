<?php
	defined('_SECURITY') or die("Access denied.");
	
	global $language;
	buffer::start();
	$id = varURL::getParam('id');
	$lid = varURL::getParam('lid');
	if (!$lid) $lid = 0;
	if (!$c = page::get($id, $lid)) {
?>
<h4><?php echo t("Can't find this page."); ?></h4>
<?php
	}
	else {
?>
<h3><?php echo t('Edit page'); ?></h3>
<form id="page-edit-form" onsubmit="return false;">
	<p><a href="#page,do:list"><?php echo t('Back to list'); ?></a></p>
	<p>
		<?php echo t('Language version'); ?>
		<select name="lid">
			<?php
				$langlist = $language->getLanguageList();
				foreach ($langlist as $lang) {
					print '<option value="' . $lang['lid'] . ($lang['lid'] == $lid ? '" selected="selected"' : '"')
					. ' onclick="javascript:window.location.href=\'#page,do:edit,id:' . $id .',lid:' . $lang['lid'] . '\';"'
					. '>' . $lang['displayname'] . '</option>';
				}
			?>
		</select>
	</p>
	<p id="page-edit-title">
		<label for="page-edit-title-box"><?php echo t('Title'); ?></label><br />
		<input id="page-edit-title-box" type="text" name="title" size="50" value="<?php echo $c['title']; ?>" />
	</p>
	<p>
		<label for="page-edit-content-box"><?php echo t('Content'); ?></label><br />
		<textarea id="page-edit-content-box" name="content" rows="15" cols="80" style="width: 90%">
			<?php echo $c['content'] ?>
		</textarea>
	</p>
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="lid" value="<?php echo varURL::getParam('lid'); ?>" />
	<input type="button" onclick="javascript:submitPageEdit();" value="<?php echo t('Submit'); ?>" />
</form>
<?php
	}
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Edit content'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>