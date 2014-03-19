<?php
namespace Posibrain;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use Monolog\Logger;
include_once (__DIR__ . '/tools.php');

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class Positroner implements IPositroner
{

	private static $logger = NULL;

	public $positrons;

	public function __construct($config)
	{
		// Logger
		if (NULL == self::$logger) {
			self::$logger = new Logger(__CLASS__);
			if (! empty($params) && isset($params['loggerHandler'])) {
				self::$logger->pushHandler($params['loggerHandler']);
			}
		}
		
		$this->loadPositrons($config);
	}

	public function loadPositrons($config)
	{
		$files = glob(dirname(__FILE__) . '/Positron/*/*Positron.php');
		if (empty($files))
			$files = array();
		
		foreach ($files as $file) {
			require_once ($file);
			$className = preg_replace('!^.*(Posibrain/Positron/.*/.*Positron).*$!ui', '$1', $file);
			$className = '\\' . str_replace('/', '\\', $className);
			$positron = new $className($config);
			$this->positrons[] = $positron;
		}
		return $this->positrons;
	}

	public function getPostitrons()
	{
		if (empty($this->positrons))
			$this->loadPositrons();
		return $this->positrons;
	}

	public function findPositron($id)
	{
		if (! isset($this->positrons[$id])) {
			return null;
		}
		return $this->positrons[$id];
	}

	public function updatePositron($id, $state)
	{}

	public function callPre($functionName, $actionArguments = array())
	{
		if (empty($this->positrons)) {
			return $actionArguments;
		}
		foreach ($this->positrons as $positron) {
			$request = $positron->$functionName($actionArguments);
		}
		return $request;
	}

	public function callPost($functionName, $actionArguments = array(), $currentAnswser = array())
	{
		if (empty($this->positrons)) {
			return $currentAnswser;
		}
		foreach ($this->positrons as $positron) {
			$currentAnswser = $positron->$functionName($actionArguments, $currentAnswser);
		}
		return $currentAnswser;
	}
}