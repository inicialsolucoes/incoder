<?php

/**
 * Request
 */
class Request
{
	/**
	 * @var array
	 */
	private $__post;

	/**
	 * @var array
	 */
	private $__get;

	/**
	 * @var array
	 */
	private $__request;

	/**
	 * @var string
	 */
	private $__method;

	/**
	 * @return Request
	 */
	public function __construct()
	{
		$this->__post    = (array)  @$_POST;
		$this->__get     = (array)  @$_GET;
		$this->__request = (array)  @$_REQUEST;
		$this->__method  = (string) @$_SERVER['REQUEST_METHOD'];

		return $this;
	}

	/**
	 * @param  string $param
	 * @return void
	 */
	private function __unsetParam($param)
	{
		if (isset($this->__get[$param])) {
			unset($this->__get[$param]);
		}

		if (isset($this->__post[$param])) {
			unset($this->__post[$param]);
		}

		if (isset($this->__request[$param])) {
			unset($this->__request[$param]);
		}
	}

	/**
	 * Get request method
	 * @return string
	 */
	public function getMethod()
	{
		return $this->__method;
	}

	/**
	 * @param  string $param
	 * @param  string $method (GET, POST, REQUEST)
	 * @return mixed
	 */
	public function getParam($param = null, $method = null, $unset = false)
	{
		$return = null;

		switch (strtoupper($method))
		{
			case 'GET':

				if (empty($param)) {
					$return = $this->__get;
				}

				if (!empty($param) && isset($this->__get[$param])) {
					$return =  $this->__get[$param];
				}

				if ($unset) {
					$this->__unsetParam($param);
				}

				return $return;

				break;

			case 'POST':

				if (empty($param)) {
					$return = $this->__post;
				}

				if (!empty($param) && isset($this->__post[$param])) {
					$return = $this->__post[$param];
				}

				if ($unset) {
					$this->__unsetParam($param);
				}

				return $return;

				break;

			case 'REQUEST':

				if (empty($param)) {
					$return = $this->__request;
				}

				if (!empty($param) && isset($this->__request[$param])) {
					$return = $this->__request[$param];
				}

				if ($unset) {
					$this->__unsetParam($param);
				}

				return $return;

				break;

			default:

				if (empty($param)) {
					$return = $this->__request;
				}

				if (!empty($param) && isset($this->__request[$param])) {
					$return = $this->__request[$param];
				}

				if ($unset) {
					$this->__unsetParam($param);
				}

				return $return;

				break;
		}
	}

	/**
	 * @param  string $param
	 * @param  string $method
	 * @return mixed
	 */
	public static function param($param = null, $method = null)
	{
		$request = new Request();

		return $request->getParam($param, $method);
	}
}