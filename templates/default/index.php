<?php
	defined('_SECURITY') or die("Access denied.");
	global $template, $header;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >
<head>
	<?php echo $header->getHeader(); ?>
	<link rel="stylesheet" href="templates/default/css/template.css" type="text/css" />
</head>
<body>
	<div id="page">
		<div id="page-wrapper">
			<div id="topcol">
				<div class="wrapper">
					<div class="inner-wrapper">&nbsp;</div>
				</div>
			</div>
			<div id="header">
				<div class="wrapper">
					<div class="inner-wrapper">
						<div id="logo">
							<a href="<?php if (admin::adminPage()) echo '#page,do:list'; else echo '#page,id:1'; ?>"><?php echo t(config::name); ?></a>
						</div>
						<div id="header-news"><?php echo $template->getContent('latestnews'); ?></div>
						<div id="language"><?php echo $template->getContent('language'); ?></div>
					</div>
				</div>
			</div>
			<div id="body">
				<div class="wrapper">
					<div id="left-column">
						<?php echo $template->getContent('left'); ?>
					</div>
					<div id="main-column">
						<div id="message">&nbsp;</div>
						<div id="main"><script type="text/javascript">processChange(hash);</script></div>
					</div>
					<div id="for-hack-only"></div>
				</div>
			</div>
			<div id="footer">
				<?php file::inc('includes/copyright.php'); ?>
			</div>
		</div>
	</div>
</body>
</html>