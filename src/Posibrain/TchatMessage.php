<?php
namespace Posibrain;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class TchatMessage
{

	protected $name;

	protected $message;

	protected $date;

	public function __construct($message, $name = '', $date = 0)
	{
		if (is_array($message)) {
			$this->message = $message[0];
			$this->name = $message[1];
			$this->setDate($message[2]);
		}
		else {
			$this->message = $message;
			$this->name = $name;
			$this->setDate($date);
		}
	}

	public function toArray()
	{
		return array(
			$this->message,
			$this->name,
			$this->date
		);
	}
	
	public function __toString()
	{
		return $this->name.' : '.$this->message.' ['.date('Y-m-d', $this->date).']';
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}

	public function getDate()
	{
		return $this->date;
	}

	public function setDate($date)
	{
		if ($date instanceof \DateTime) {
			$date = $date->getTimestamp();
		}
		elseif (0 == $date) {
			$date = time();
		}
		elseif (is_string($date)) {
			$date = strtotime($date);
		}
		$this->date = $date;
		return $this;
	}
}