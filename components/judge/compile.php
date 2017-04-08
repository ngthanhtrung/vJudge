<?php
	defined('_SECURITY') or die("Access denied.");
	
	$sid = varURL::get('id');
	$sub = judge::getSubmission($sid);
	if ($sub) {
		judge::updateSubmission($sid, -2); // Status: compiling
		
		// Make dir
		@mkdir('tmp/' . $sid, 0755);
		
		// Make copy batch file
		$content = 'copy uploads\\submissions\\' . $sid . '.src tmp\\' . $sid . '\\' . $sid . '.src' . "\r\n";
		
		$prob = judge::getProblem($sub['problem'], 0);
		$code = $prob['code'];
		$numtest = $prob['numtest'];
		
		for ($i = 1; $i <= $numtest; $i++) {
			$content .= 'copy uploads\\tests\\' . $code . '\\' . $i . '.in tmp\\' . $sid . '\\' . $i . '.in'."\r\n";
			$content .= 'copy uploads\\tests\\' . $code . '\\' . $i . '.ans tmp\\' . $sid . '\\' . $i . '.ans'."\r\n";
		}
		$batfile = 'tmp\\' . $sid . '\\copyfile.bat';
		file::save($batfile, $content);
		
		// Make compile batch file
		$pl = judge::getPl($sub['proglanguage']);
		$str = $pl['compilerfile'];
		$content = str_replace('%name','tmp\\' . $sid . '\\' . $sid . '.src', $str);
		$batfile = 'tmp\\' . $sid . '\\compile.bat';
		file::save($batfile, $content);
		
		// Make run batch file
		$content = '';
		$timelimit = $prob['maxtime'];
		for ($i = 1; $i <= $numtest; $i++) {
			$content .= 'compilers\\judge.exe -t ' . $timelimit . ' tmp\\' . $sid . '\\' . $sid . '.exe <tmp\\' . $sid . '\\' . $i . '.in >tmp\\' . $sid . '\\' . $i . '.out'."\r\n";
		}
		$batfile = 'tmp\\' . $sid . '\\runtest.bat';
		file::save($batfile, $content);
		
		//Make judge bat
		$content = '';
		for ($i = 1; $i <= $numtest; $i++) {
			$content .= 'fc tmp\\' . $sid . '\\' . $i . '.out tmp\\' . $sid . '\\' . $i . '.ans' . "\r\n";
		}
		$batfile = 'tmp\\' . $sid . '\\judge.bat';
		file::save($batfile, $content);
		
		exec('tmp\\' . $sid . '\\copyfile.bat');
		
		buffer::start();
		passthru('tmp\\'.$sid.'\\compile.bat');
		$res = buffer::get();
		buffer::end();
		
		$failedpat = $pl['failedpat'];
		if (strpos($res, $failedpat) !== false) {
			judge::updateSubmission($sid, -3); // Status: error
			return; // Compile error
		}
			
		exec('tmp\\' . $sid . '\\runtest.bat');
		
		buffer::start();
		passthru('tmp\\' . $sid . '\\judge.bat');
		$res = buffer::get();
		buffer::end();
		
		$successpat = 'FC: no differences encountered';
		$ncount = substr_count($res, $successpat);
		$re = floor($ncount / $numtest * 100);
		
		judge::updateSubmission($sid, $re); // Complete, set status = mark
	}
?>