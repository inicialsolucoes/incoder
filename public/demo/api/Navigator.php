<?php

/**
 * @see Settings
 */
require_once 'Settings.php';

/**
 * Navigator
 */
class Navigator
{
	/**
	 * @var string
	 */
	private $__basePath;

	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->__basePath = Settings::$basePath;
	}

	/**
	 * @param  string $path
	 * @return array
	 */
	public function listPath($path = null)
	{
		$paths = array();

		if (is_dir($this->__basePath.$path)) {

			foreach (scandir($this->__basePath.$path) as $file) {

				if (is_file($this->__basePath.$path.$file)) {

					array_push($paths, array(
						'type' => (string) 'file',
						'path' => (string) $path,
						'file' => (string) $file
					));

				} else {

					if ($file != '.' && $file != '..') {

						array_push($paths, array(
							'type' => (string) 'folder',
							'path' => (string) $path,
							'file' => (string) $file.'/'
						));
					}

				}
			}
		}

		return $paths;
	}

	/**
	 * @param  string $path
	 * @return array
	 */
	public static function readPath($path = null)
	{
		$navigator = new Navigator();

		return $navigator->listPath($path);
	}
}