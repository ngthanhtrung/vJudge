<?php
	defined('_SECURITY') or die("Access denied.");
	
	if (!varPOST::get('code')) echo t('Status:') . ' ' . t('Ready');
	else {
		//Process Upload
		$sid = judge::nextIdSubmission();
		$re = judge::upload($sid);
		if ($re == UPLOAD_SUCCESS) {
			buffer::start();
			?>
			<script type="text/javascript">
				get2('com=judge&mod=compile&id=<?php echo $sid; ?>');
				parent.window.location.href = './#judge,do:submission';
			</script>;
			<?php
			$content = buffer::get();
			buffer::end();
			echo $content;
		}
		else {
			echo t('Status:') . '&nbsp;';
			switch ($re) {
				case (NOT_LOGGED_IN):
					echo t('You are not logging in');
					break;
				case (FILE_TOO_LARGE) :
					echo t('File\'s too large');
					break;
				case (UPLOAD_FAILED) :
					echo t('Upload failed');
					break;
				case (INCORRECT_PROBLEM_CODE) :
					echo t('Incorrect problem code');
			}
		}
	}
?>