<?php
namespace Posibrain;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class AnalysedRequest extends TchatMessage
{

	private $rawRequest;
	
	/*
	 * (non-PHPdoc) @see \Posibrain\Request::__construct()
	 */
	public function __construct($message, $name = '', $date = 0, TchatMessage $rawRequest = null)
	{
		parent::__construct($message, $name = '', $date = 0);
		if (is_array($message)) {
			$this->rawRequest = $message[3];
		}
		if ($message instanceof TchatMessage) {
			$this->message = $message->getMessage();
			$this->name = $message->getName();
			$this->setDate($message->getDate());
			$this->rawRequest = $message;
		}
		else {
			$this->rawRequest = $rawRequest;
		}
	}

	public function getRawRequest()
	{
		return $this->rawRequest;
	}

	public function setRawRequest($rawRequest)
	{
		$this->rawRequest = $rawRequest;
		return $this;
	}
}