<?php
/**
 * @see Settings
 */
require_once 'Settings.php';

/**
 * @see Request
 */
require_once 'Request.php';

/**
 * @see Response
 */
require_once 'Response.php';

/**
 * App
 */
class App
{
	/**
	 * @var string
	 */
	private $__routeUri;

	/**
	 * @return void
	 */
	public function __construct()
	{
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *');
		// header('Access-Control-Allow-Credentials: true');
		// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

		if (Settings::$authProtected) {

			try {

				$authUser = @$_SERVER['PHP_AUTH_USER'];
				$authPass = @$_SERVER['PHP_AUTH_PW'];
				$authPass = $authPass ? md5($authPass) : $authPass;

				if (!($authUser == Settings::$authUser && $authPass  == Settings::$authPass)) {
					throw new Exception("Error Processing Request", 1);
				}

			} catch (Exception $e) {

				header('WWW-Authenticate: Basic realm="inCoder"');
			    header('HTTP/1.0 401 Unauthorized');
			    echo 'You must be logged in!';
			    exit;
			}
		}

		$this->__setRouteUri();
	}

	/**
	 * @return void
	 */
	private function __setRouteUri()
	{
		// SERVER

		$phpSelf     = $_SERVER['PHP_SELF'];
		$queryString = $_SERVER['QUERY_STRING'];
		$requestUri  = $_SERVER['REQUEST_URI'];

		// BASE URI

		$phpSelf     = $_SERVER['PHP_SELF'];
		$phpSelfData = explode('/', $phpSelf);
		$phpFileName = end($phpSelfData);

		$baseUri = str_replace($phpFileName, '', $phpSelf);

		// ROUTE URI

		$this->__routeUri = '/'.str_replace(array($baseUri, $queryString,'?'), '', $requestUri);
	}

	/**
	 * @param  string $route
	 * @param  function $callback
	 * @return mixed
	 */
	public function route($route, $callback)
	{
		if (strpos($this->__routeUri, $route) === 0) {
			if (is_callable($callback)) {
				return $callback(new Request(), new Response());
			}
		}
	}
}