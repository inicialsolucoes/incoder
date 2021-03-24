<?php

/**
 * Settings
 */
class Settings
{
	/**
	 * API Auth Protection
	 * @var boolean
	 */
	public static $authProtected = true;

	/**
	 * API Auth User
	 * @var string
	 */
	public static $authUser = 'api';

	/**
	 * API Auth Password
	 * Encrypted with MD5
	 * @var string
	 */
	public static $authPass = '8a5da52ed126447d359e70c05721a8aa'; // Default: api

	/**
	 * Absolute File Base Path
	 * @var string
	 */
	public static $basePath = './';
}

/**
 * Set base path dynamically for example
 * Remove or comment this setup
 * if you set Setting::$basePath statically
 * @var string
 */
Settings::$basePath = dirname(__FILE__).'/../';