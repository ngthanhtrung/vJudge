<?php
	defined('_SECURITY') or die("Access denied.");
	global $template, $header;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >
<head>
	<?php echo $header->getHeader(); ?>
	<link rel="stylesheet" href="templates/default/css/blank.css" type="text/css" />
</head>
<body>
<?php file::inc_once("components/" . varURL::get('com') . "/" . varURL::get('mod') . ".php"); ?>
</body>
</html>