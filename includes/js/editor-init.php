<?php
	defined('_SECURITY') or die("Access denied.");
	
	global $language;
?>
tinyMCE.init({
	mode : "none",
	theme : "simple",
	language : "<?php echo $language->getLanguageCode(); ?>"
});