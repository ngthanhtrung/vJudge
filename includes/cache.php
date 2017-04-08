<?php
	defined('_SECURITY') or die("Access denied.");
	
	define('CACHE_ERROR_RETURN', 1);
	define('CACHE_ERROR_DIE', 8);
	
	class cache {
		var $cacheDir = 'tmp/';
		var $caching = true;
		var $lifeTime = 3600;
		var $fileLocking = true;
		var $refreshTime;
		var $file;
		var $fileName;
		var $writeControl = true;
		var $readControl = true;
		var $readControlType = 'crc32';
		var $errorMode = CACHE_ERROR_RETURN;
		var $id;
		var $group;
		var $memoryCaching = false;
		var $onlyMemoryCaching = false;
		var $memoryCachingArray = array();
		var $memoryCachingCounter = 0;
		var $memoryCachingLimit = 1000;
		var $fileNameProtection = true;
		var $automaticSerialization = true;
		var $automaticCleaningFactor = 0;
		var $hashedDirectoryLevel = 0;
		var $hashedDirectoryUmask = 0700;
		var $errorHandlingAPIBreak = false;
		
		function cache($options = array(NULL)) {
			foreach($options as $key => $value)
				$this->setOption($key, $value);
		}
		
		function setOption($name, $value) {
			$availableOptions = array('errorHandlingAPIBreak', 'hashedDirectoryUmask', 'hashedDirectoryLevel',
			'automaticCleaningFactor', 'automaticSerialization', 'fileNameProtection', 'memoryCaching',
			'onlyMemoryCaching', 'memoryCachingLimit', 'cacheDir', 'caching', 'lifeTime', 'fileLocking',
			'writeControl', 'readControl', 'readControlType', 'errorMode');
			if (in_array($name, $availableOptions))
				$this->$name = $value;
		}
		
		function get($id, $group = 'default', $doNotTestCacheValidity = false) {
			$this->id = $id;
			$this->group = $group;
			$data = false;
			if ($this->caching) {
				$this->setRefreshTime();
				$this->setFileName($id, $group);
				clearstatcache();
				if ($this->memoryCaching) {
					if (isset($this->memoryCachingArray[$this->file])) {
						if ($this->automaticSerialization)
							return unserialize($this->memoryCachingArray[$this->file]);
						return $this->memoryCachingArray[$this->file];
					}
					if ($this->onlyMemoryCaching)
						return false;
				}
				if (($doNotTestCacheValidity) || (is_null($this->refreshTime))) {
					if (file_exists($this->file)) {
						$data = $this->read();
					}
				} elseif ((file_exists($this->file)) && (@filemtime($this->file) > $this->refreshTime))
						$data = $this->read();
						
				if (($data) and ($this->memoryCaching)) {
					$this->memoryCacheAdd($data);
				}
				if (($this->automaticSerialization) and (is_string($data)))
					$data = unserialize($data);
				
				return $data;
			}
			return false;
		}
		
		function save($data, $id = NULL, $group = 'default') {
			if ($this->caching) {
				if ($this->automaticSerialization)
					$data = serialize($data);
					
				if (isset($id))
					$this->setFileName($id, $group);
					
				if ($this->memoryCaching) {
					$this->memoryCacheAdd($data);
					if ($this->onlyMemoryCaching)
						return true;
				}
				
				if ($this->automaticCleaningFactor > 0
				&& ($this->automaticCleaningFactor == 1 || mt_rand(1, $this->automaticCleaningFactor) == 1))
					$this->clean(false, 'old');
					
				if ($this->writeControl) {
					$res = $this->writeAndControl($data);
					if (is_bool($res)) {
						if ($res)
							return true;
							
						// if $res if false, we need to invalidate the cache
						@touch($this->file, time() - 2*abs($this->lifeTime));
						return false;
					}
				} else
					$res = $this->write($data);
					
				if (is_object($res)) {
					// $res is a error object 
					if (!($this->errorHandlingAPIBreak))
						return false; // we return false (old API)
				}
				return $res;
			}
			return false;
		}
		
		function remove($id, $group = 'default') {
			$this->setFileName($id, $group);
			if ($this->memoryCaching) {
				if (isset($this->memoryCachingArray[$this->file])) {
					unset($this->memoryCachingArray[$this->file]);
					$this->memoryCachingCounter = $this->memoryCachingCounter - 1;
				}
				if ($this->onlyMemoryCaching)
					return true;
			}
			if (!file_exists($this->file)) return true;
			return $this->unlink($this->file);
		}
		
		function clean($group = false, $mode = 'ingroup') {
			return $this->cleanDir($this->cacheDir, $group, $mode);
		}
		
		function setToDebug() {
			$this->setOption('errorMode', CACHE_ERROR_DIE);
		}
		
		function setLifeTime($newLifeTime) {
			$this->lifeTime = $newLifeTime;
			$this->setRefreshTime();
		}
		
		function saveMemoryCachingState($id, $group = 'default') {
			if ($this->caching) {
				$array = array(
					'counter' => $this->memoryCachingCounter,
					'array' => $this->memoryCachingArray
				);
				$data = serialize($array);
				$this->save($data, $id, $group);
			}
		}
		
		function getMemoryCachingState($id, $group = 'default', $doNotTestCacheValidity = false) {
			if ($this->caching) {
				if ($data = $this->get($id, $group, $doNotTestCacheValidity)) {
					$array = unserialize($data);
					$this->memoryCachingCounter = $array['counter'];
					$this->memoryCachingArray = $array['array'];
				}
			}
		}
		
		function lastModified() {
			return @filemtime($this->file);
		}
		
		function raiseError($msg, $code) {
			switch ($this->errorMode) {
				case 'CACHE_ERROR_RETURN':
					echo("Cache error: $msg.");
					break;
				case 'CACHE_ERROR_DIE':
					die("Cache error: $msg.");
			}
		}
		
		function extendLife() {
			@touch($this->file);
		}
		
		function setRefreshTime() {
			if (is_null($this->lifeTime)) {
				$this->refreshTime = null;
			} else {
				$this->refreshTime = time() - $this->lifeTime;
			}
		}
		
		function unlink($file) {
			if (!@unlink($file)) {
				return $this->raiseError('Unable to remove cache!', -3);
			}
			return true;
		}
		
		function cleanDir($dir, $group = false, $mode = 'ingroup') {
			if ($this->fileNameProtection)
				$motif = ($group) ? 'cache_' . md5($group) . '_' : 'cache_';
			else $motif = ($group) ? 'cache_' . $group . '_' : 'cache_';
			
			if ($this->memoryCaching) {
				foreach($this->memoryCachingArray as $key => $v) {
					if (strpos($key, $motif) !== false) {
						unset($this->memoryCachingArray[$key]);
						$this->memoryCachingCounter = $this->memoryCachingCounter - 1;
					}
				}
				if ($this->onlyMemoryCaching)
					return true;
			}
			if (!($dh = opendir($dir))) {
				return $this->raiseError('Unable to open cache directory!', -4);
			}
			$result = true;
			while ($file = readdir($dh)) {
				if (($file != '.') && ($file != '..')) {
					if (substr($file, 0, 6)=='cache_') {
						$file2 = $dir . $file;
						if (is_file($file2)) {
							switch (substr($mode, 0, 9)) {
								case 'old':
									// files older than lifeTime get deleted from cache
									if (!is_null($this->lifeTime))
										if ((time() - @filemtime($file2)) > $this->lifeTime)
											$result = ($result and ($this->unlink($file2)));
									break;
								case 'notingroup':
									if (strpos($file2, $motif) === false)
										$result = ($result and ($this->unlink($file2)));
									break;
								case 'callback_':
									$func = substr($mode, 9, strlen($mode) - 9);
									if ($func($file2, $group))
										$result = ($result and ($this->unlink($file2)));
									break;
								case 'ingroup':
								default:
									if (strpos($file2, $motif) !== false)
										$result = ($result and ($this->unlink($file2)));
									break;
							}
						}
						if ((is_dir($file2)) and ($this->hashedDirectoryLevel>0))
							$result = ($result and ($this->cleanDir($file2 . '/', $group, $mode)));
					}
				}
			}
			return $result;
		}
		
		function memoryCacheAdd($data) {
			$this->memoryCachingArray[$this->file] = $data;
			if ($this->memoryCachingCounter >= $this->memoryCachingLimit) {
				list($key, ) = each($this->memoryCachingArray);
				unset($this->memoryCachingArray[$key]);
			} else $this->memoryCachingCounter = $this->memoryCachingCounter + 1;
		}
		
		function setFileName($id, $group) {
			if ($this->fileNameProtection) {
				$suffix = 'cache_'.md5($group).'_'.md5($id);
			} else $suffix = 'cache_'.$group.'_'.$id;
			
			$root = $this->cacheDir;
			if ($this->hashedDirectoryLevel>0) {
				$hash = md5($suffix);
				for ($i=0 ; $i<$this->hashedDirectoryLevel ; $i++)
					$root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
			}
			$this->fileName = $suffix;
			$this->file = $root . $suffix;
		}
		
		function read() {
			$fp = @fopen($this->file, "rb");
			if ($this->fileLocking) @flock($fp, LOCK_SH);
			if ($fp) {
				clearstatcache();
				$length = @filesize($this->file);
				$mqr = get_magic_quotes_runtime();
				set_magic_quotes_runtime(0);
				if ($this->readControl) {
					$hashControl = @fread($fp, 32);
					$length = $length - 32;
				}
				if ($length) $data = @fread($fp, $length);
				else $data = '';
				
				set_magic_quotes_runtime($mqr);
				if ($this->fileLocking) @flock($fp, LOCK_UN);
				@fclose($fp);
				if ($this->readControl) {
					$hashData = $this->hash($data, $this->readControlType);
					if ($hashData != $hashControl) {
						if (!(is_null($this->lifeTime))) {
							@touch($this->file, time() - 2*abs($this->lifeTime));
						} else {
							@unlink($this->file);
						}
						return false;
					}
				}
				return $data;
			}
			return $this->raiseError('Unable to read cache!', -2); 
		}
		
		function write($data) {
			if ($this->hashedDirectoryLevel > 0) {
				$hash = md5($this->fileName);
				$root = $this->cacheDir;
				for ($i=0 ; $i<$this->hashedDirectoryLevel ; $i++) {
					$root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
					if (!(@is_dir($root)))
						@mkdir($root, $this->hashedDirectoryUmask);
				}
			}
			$fp = @fopen($this->file, "wb");
			if ($fp) {
				if ($this->fileLocking) @flock($fp, LOCK_EX);
				if ($this->readControl)
					@fwrite($fp, $this->hash($data, $this->readControlType), 32);
					
				$mqr = get_magic_quotes_runtime();
				set_magic_quotes_runtime(0);
				@fwrite($fp, $data);
				set_magic_quotes_runtime($mqr);
				if ($this->fileLocking) @flock($fp, LOCK_UN);
				@fclose($fp);
				return true;
			}
			return $this->raiseError('Unable to write cache file: '.$this->file, -1);
		}
		
		function writeAndControl($data) {
			$result = $this->write($data);
			if (is_object($result))
				return $result;
			$dataRead = $this->read();
			if (is_object($dataRead))
				return $dataRead;
			if ((is_bool($dataRead)) && (!$dataRead))
				return false;
			return ($dataRead == $data);
		}
		
		function hash($data, $controlType) {
			switch ($controlType) {
				case 'md5':
					return md5($data);
				case 'crc32':
					return sprintf('% 32d', crc32($data));
				case 'strlen':
					return sprintf('% 32d', strlen($data));
				default:
					return $this->raiseError('Unknown controlType! (available values are only \'md5\', \'crc32\', \'strlen\')', -5);
			}
		}
	}
?>