<?php

/**
 * @see Settings
 */
require_once 'Settings.php';

/**
 * Editor
 */
class Editor
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
	 * Get file contents
	 * @param  string $path Path from base path
	 * @return array|bool
	 */
	public function getFile($path)
	{
		if (is_file($this->__basePath.$path)) {

			return array (
				'type'    => mime_content_type($this->__basePath.$path),
				'content' => file_get_contents($this->__basePath.$path)
			);
		}

		return false;
	}

	/**
	 * Save file contents
	 * @param  string $path
	 * @param  string $content
	 * @return array|bool
	 */
	public function saveFile($path, $content = null)
	{
		if (file_put_contents($this->__basePath.$path, $content)) {

			return array (
				'type'    => mime_content_type($this->__basePath.$path),
				'content' => file_get_contents($this->__basePath.$path)
			);
		}

		return false;
	}


	/**
	 * Get/Save file contents
	 * @param  string $path
	 * @param  string $content
	 * @return array|bool
	 */
	public static function file($path, $content = null)
	{
		$editor =  new Editor();

		if ($content) {

			return $editor->saveFile($path, $content);

		} else {

			return $editor->getFile($path);
		}

		return false;
	}
}