<?php
	defined('_SECURITY') or die("Access denied.");
	
	buffer::start();
?>
<h3><?php echo t('Latest news'); ?></h3>
<p>
<?php
	if ($list = news::latestNews()) {
		foreach ($list as $item) {
			$userinfo = user::getUser($item['createdby']);
?>
<h4><?php echo $item['title']; ?></h4>
<div class="small">
	<?php echo t('<b>%user</b> sent at <b>%time</b>', $userinfo['displayname'], date('h:i:s A - d/m/Y', strtotime($item['created']))); ?>
</div>
<div><?php echo $item['content']; ?></div>
<?php
		}
	}
?>
</p>
<?php
	$content = buffer::get();
	buffer::end();
	
	global $template;
	$mod = array(
		'title' => t('Latest news'),
		'content' => $content,
		'wrap' => false,
		'cache' => false
	);
	$template->setModule($mod);
?>