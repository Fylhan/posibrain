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

	public function __construct($config, $params=array())
	{
		// Logger
		if (NULL == self::$logger) {
			self::$logger = new Logger(__CLASS__);
			if (! empty($params) && isset($params['loggerHandler'])) {
				self::$logger->pushHandler($params['loggerHandler']);
			}
		}
		
		$this->loadPositrons($config, $params);
	}

	public function loadPositrons($config, $params=array())
	{
		$files = glob(dirname(__FILE__) . '/Positron/*/*Positron.php');
		if (empty($files))
			$files = array();
		
		foreach ($files as $file) {
			require_once ($file);
			$className = preg_replace('!^.*(Posibrain/Positron/.*/.*Positron).*$!ui', '$1', $file);
			$className = '\\' . str_replace('/', '\\', $className);
			$positron = new $className($config, $params);
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

	public function isTriggered(TchatMessage $request, $currentValue = true)
	{
		if (empty($this->positrons)) {
			return $currentValue;
		}
		foreach ($this->positrons as $positron) {
			$currentValue = $positron->isTriggered($request, $currentValue);
		}
		return $currentValue;
	}

	public function loadMemory(AnalysedRequest $request, $currentMemory = null)
	{
		if (empty($this->positrons)) {
			return $currentMemory;
		}
		foreach ($this->positrons as $positron) {
			$currentMemory = $positron->loadMemory($request, $currentMemory);
		}
		return $currentMemory;
	}

	public function analyseRequest(TchatMessage $request, AnalysedRequest $currentAnalysedRequest = null)
	{
		if (empty($this->positrons)) {
			return $currentAnalysedRequest;
		}
		foreach ($this->positrons as $positron) {
			$currentAnalysedRequest = $positron->analyseRequest($request, $currentAnalysedRequest);
		}
		return $currentAnalysedRequest;
	}

	public function generateSymbolicAnswer(AnalysedRequest $request, $memory = null, TchatMessage $currentAnswer = null)
	{
		if (empty($this->positrons)) {
			return $currentAnswer;
		}
		foreach ($this->positrons as $positron) {
			$currentAnswser = $positron->generateSymbolicAnswer($request, $memory, $currentAnswer);
		}
		return $currentAnswser;
	}
	
	public function provideMeaning(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
	{
		if (empty($this->positrons)) {
			return $currentAnswer;
		}
		foreach ($this->positrons as $positron) {
			$currentAnswser = $positron->provideMeaning($request, $memory, $answer, $currentAnswer);
		}
		return $currentAnswser;
	}

	public function beautifyAnswer(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
	{
		if (empty($this->positrons)) {
			return $currentAnswer;
		}
		foreach ($this->positrons as $positron) {
			$currentAnswser = $positron->beautifyAnswer($request, $memory, $answer, $currentAnswer);
		}
		return $currentAnswser;
	}
}