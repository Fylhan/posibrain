<?php

namespace Posibrain;

use Monolog\Logger;

include_once(__DIR__.'/tools.php');

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 */
class TchatBot implements ITchatBot
{
	private static $logger = NULL;
	private $config;
	private $brain;

	public function __construct($id='', $lang='', $params=array()) {
		// Logger
		if (NULL == self::$logger) {
			self::$logger = new Logger(__CLASS__);
			if (!empty($params) && isset($params['loggerHandler'])) {
				self::$logger->pushHandler($params['loggerHandler']);
			}
		}

		// Config
		$this->config = new TchatBotConfig($id, $lang, $params);
		
		// Brain Manager
		$this->brain = new Positroner($params);
	}

	public function isTriggered($userMessage, $userName='', $dateTime='') {
		$request = $this->brain->callPre('preIsTriggered', array($userMessage, $userName, $dateTime));
		$triggered = $this->brain->callPost('postIsTriggered', $request);
		return $triggered;
	}

	public function generateAnswer($userMessage, $userName='', $dateTime='') {
		$request = $this->brain->callPre('preGenerateAnswer', array($userMessage, $userName, $dateTime));
		$answer = $this->brain->callPost('postGenerateAnswer', $request);
		return $answer;
	}
	
	public function config() {
		return $this->config;
	}
	public function setConfig($config) {
		$this->config = $config;
	}
	
	public function setBrain($brain) {
		$this->brain = $brain;
	}
}

