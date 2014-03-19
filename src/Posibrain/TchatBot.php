<?php
namespace Posibrain;

use Monolog\Logger;
include_once (__DIR__ . '/tools.php');

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class TchatBot implements ITchatBot
{

	private static $logger = NULL;

	private $config;

	private $brain;

	public function __construct($id = '', $lang = '', $params = array())
	{
		// Logger
		if (NULL == self::$logger) {
			self::$logger = new Logger(__CLASS__);
			if (! empty($params) && isset($params['loggerHandler'])) {
				self::$logger->pushHandler($params['loggerHandler']);
			}
		}
		
		// Config
		$this->config = new TchatBotConfig($id, $lang, $params);
		
		// Brain Manager
		$this->brain = new Positroner($this->config);
	}

	public function isTriggered($userMessage, $userName = '', $dateTime = 0)
	{
		// Check params
		if ($dateTime instanceof \DateTime) {
			$dateTime = $dateTime->getTimestamp();
		}
		elseif (0 == $dateTime) {
			$dateTime = time();
		}
		elseif (is_string($dateTime)) {
			$dateTime = strtotime($dateTime);
		}
		// Pre
		$request = $this->brain->callPre('preIsTriggered', array(
			$userMessage,
			$userName,
			$dateTime
		));
		// Post
		$triggered = $this->brain->callPost('postIsTriggered', $request);
		return $triggered;
	}

	public function generateAnswer($userMessage, $userName = '', $dateTime = 0)
	{
		// Check params
		if ($dateTime instanceof \DateTime) {
			$dateTime = $dateTime->getTimestamp();
		}
		elseif (0 == $dateTime) {
			$dateTime = time();
		}
		elseif (is_string($dateTime)) {
			$dateTime = strtotime($dateTime);
		}
		// Pre
		$request = $this->brain->callPre('preGenerateAnswer', array(
			$userMessage,
			$userName,
			$dateTime
		));
		// Post
		$answer = $this->brain->callPost('postGenerateAnswer', $request);
		return $answer;
	}

	public function getConfig()
	{
		return $this->config;
	}

	public function setConfig($config)
	{
		$this->config = $config;
	}

	public function setBrain($brain)
	{
		$this->brain = $brain;
	}
}

