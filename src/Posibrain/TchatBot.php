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
		$this->brain = new Positroner($this->config, $params);
	}

	public function isTriggered($userMessage, $userName = '', $dateTime = 0)
	{
		$request = new TchatMessage($userMessage, $userName, $dateTime);
		return $this->brain->isTriggered($request);
	}

	public function generateAnswer($userMessage, $userName = '', $dateTime = 0)
	{
		$request = new TchatMessage($userMessage, $userName, $dateTime);
		$request = $this->brain->analyseRequest($request);
		$memory = $this->brain->loadMemory($request);
		$answer = $this->brain->generateSymbolicAnswer($request);
		$answer = $this->brain->beautifyAnswer($request, $memory, $answer);
		if (null == $answer) {
			$answer = new TchatMessage('Ssqdijoezf ? Jkfd.', 'QTzbn');
		}
		return $answer->toArray();
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

