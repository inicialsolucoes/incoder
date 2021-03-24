<?php

/**
 * Response
 */
class Response
{
	/**
	 * @var array
	 */
	private $__data;

	/**
	 * @var boolean
	 */
	private $__status;

	/**
	 * @var string
	 */
	private $__message;

	/**
	 * @param mixed   $data
	 * @param boolean $status
	 * @return Response
	 */
	public function __construct($data = null, $status = true, $message = null)
	{
		$this->setData   ($data);
		$this->setStatus ($status);
		$this->setMessage($message);

		return $this;
	}

	/**
	 * @param mixed $data
	 * @return Response
	 */
	public function setData($data)
	{
		$this->__data = (array) $data;

		return $this;
	}

	/**
	 * @param bool $status
	 * @return Response
	 */
	public function setStatus($status)
	{
		$this->__status = (bool) $status;

		return $this;
	}

	/**
	 * @param string $message
	 * @return Response
	 */
	public function setMessage($message)
	{
		$this->__message = (string) $message;

		return $this;
	}

	/**
	 * Output JSON
	 * @return void
	 */
	public function send()
	{
		header('Content-Type: application/json');

		print json_encode(array(
			'status'  => $this->__status,
			'message' => $this->__message,
			'data'    => $this->__data
		));

		exit();
	}
}