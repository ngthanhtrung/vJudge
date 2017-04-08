<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
	$imgdir = 'components/language/images/';
?>
<div><a href="#language,id:0">English-US <img src="<?php echo $imgdir; ?>us.png" alt="English-US" /></a></div>
<div><a href="#language,id:1">Tiếng Việt <img src="<?php echo $imgdir; ?>vn.png" alt="Vietnamese" /></a></div>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Choose your language'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>