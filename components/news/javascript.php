<?php
	defined('_SECURITY') or die("Access denied.");
?>
function submitNewsEdit() {
	post('com=news&q=do:submitedit', createParam('news-edit-form'));
}
function submitNewsAdd() {
	post('com=news&q=do:submitadd', createParam('news-add-form'));
}
function submitNewsDelete(id) {
	if (confirm("<?php echo t('Do you sure you want to delete?'); ?>"))
		post('com=news&q=do:delete', 'promt=yes&id=' + id);
}