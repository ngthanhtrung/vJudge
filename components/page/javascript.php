<?php
	defined('_SECURITY') or die("Access denied.");
?>
function submitPageEdit() {
	post('com=page&q=do:submitedit', createParam('page-edit-form'));
}
function submitPageAdd() {
	post('com=page&q=do:submitadd', createParam('page-add-form'));
}
function submitPageDelete(id) {
	if (confirm("<?php echo t('Do you sure you want to delete?'); ?>"))
		post('com=page&q=do:delete', 'promt=yes&id=' + id);
}