<?php
	defined('_SECURITY') or die("Access denied.");
	
	class menu {
		function get($id) {
			global $db;
			$query = "SELECT * FROM #__menus"
			. "\n WHERE mid = %d";
			$db->query($query, $id);
			return ($db->noRow() ? false : $db->getRow());
		}
		
		function getItems($id) {
			global $db;
			$query = "SELECT * FROM #__menu_items"
			. "\n WHERE menu = %d AND enabled = '1'"
			. "\n ORDER BY position";
			$db->query($query, $id);
			return $db->getList();
		}
		
		function getContent($menu) {
			buffer::start();
?>
<ul class="menu">
<?php
			foreach ($menu as $item) {
				if ($item['name'] == '_space') {
?>
	<li class="spacer">&nbsp;</li>
				<?php } else { ?>
	<li class="<?php echo ($item['level'] == '0' ? 'mainitem' : 'childitem'); ?>">
					<?php if ($item['link'] != '') { ?>
		<a href="<?php echo $item['link']; ?>"><?php echo t($item['name']); ?></a>
					<?php } else { ?>
		<?php echo t($item['name']); ?>
					<?php } ?>
	</li>
<?php
				}
			}
?>
</ul>
<?php
			$content = buffer::get();
			buffer::end();
			return $content;
		}
	}
?>