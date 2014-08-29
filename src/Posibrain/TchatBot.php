<?php
namespace Posibrain;

use Monolog\Logger;
include_once (__DIR__ . '/../tools.php');

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class TchatBot implements ITchatBot
{

	private static $logger = NULL;

	private $identity;

	private $positrons;

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
		$this->identity = new TchatBotIdentity($id, $lang, $params);
		
		// Brain Manager
		$this->positrons = new Positroner($this->identity, $params);
		$this->positrons->loadPositrons(@$params['positrons'], $this->identity, $params);
	}

	public function isTriggered($userMessage, $userName = '', $dateTime = 0)
	{
		$request = new TchatMessage($userMessage, $userName, $dateTime);
		return $this->positrons->isBotTriggered($request);
	}

	public function generateAnswer($userMessage, $userName = '', $dateTime = 0)
	{
		$request = new TchatMessage($userMessage, $userName, $dateTime);
		if (!$this->positrons->isBotTriggered($request)) {
			return null;
		}
		$request = $this->positrons->analyseRequest($request);
		$memory = $this->positrons->loadMemory($request);
		$answer = $this->positrons->generateSymbolicAnswer($request, $memory);
		$answer = $this->positrons->provideMeaning($request, $memory, $answer);
		$answer = $this->positrons->beautifyAnswer($request, $memory, $answer);
		if (null == $answer || ('' == $answer->getMessage() && '' == $answer->getName())) {
			$answer = new TchatMessage('Ssqdijoezf ? Jkfd.', 'QTzbn');
		}
		return $answer->toArray();
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function setConfig($config)
	{
		$this->identity = $config;
	}

	public function setBrain($brain)
	{
		$this->positrons = $brain;
	}
}

