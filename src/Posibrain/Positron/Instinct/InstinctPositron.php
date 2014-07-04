<?php
namespace Posibrain\Positron\Instinct;

use Monolog\Logger;
use Posibrain\Positron\Positron;
use Posibrain\TchatBotConfig;
use Posibrain\AnalysedRequest;
use Posibrain\TchatMessage;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class InstinctPositron extends Positron
{

	private static $logger = NULL;

	private $config;

	private $brainManager;

	private $knowledges;

	private $matching;

	public function __construct($config, $params = array())
	{
		// Logger
		if (NULL == self::$logger) {
			self::$logger = new Logger(__CLASS__);
			if (! empty($params) && isset($params['loggerHandler'])) {
				self::$logger->pushHandler($params['loggerHandler']);
			}
		}
		
		// Brain Manager
		$this->config = $config;
		$this->brainManager = new BrainManager($params);
		
		// -- Load knowledge file
		$this->knowledges = $this->brainManager->loadBrain($this->config);
	}

	public function isBotTriggered(TchatMessage $request, $currentAnswer = true)
	{
		$content = $request->getMessage();
		$triggered = (NULL != $content);
		$identity = $this->knowledges->identity;
		// Triggered by specific rules
		if (isset($identity->trigger) && ! empty($identity->trigger)) {
			// Called by his name
			if (! empty($identity->trigger->called)) {
				$triggered &= preg_match('!(?:^|\s|[_-])(' . implode('|', $identity->trigger->called) . ')(?:$|\s|[\'_-])!i', $content);
			}
			// Specific sentance
			if (! empty($identity->trigger->sentance)) {
				$triggered &= preg_match('!(' . implode('|', $identity->trigger->sentance) . ')!i', $content);
			}
		}
		return $triggered;
	}

	public function generateSymbolicAnswer(AnalysedRequest $request, $memory, TchatMessage $currentAnswer = null)
	{
		$userMessage = $request->getMessage();
		$userName = $request->getName();
		$dateTime = $request->getDate();
		
		if (null == $this->knowledges) {
			return null;
		}
		
		$identity = $this->knowledges->identity;
		$synonyms = $this->knowledges->synonyms;
		$knowledge = $this->knowledges->keywords;
		
		// -- Generate reply
		// - Check User Message
		if (empty($userMessage)) {
			$message = 'Hello';
		}
		
		// - Best keyword priority
		$keywordItem = $this->findBestPriorityKeyword($userName, $userMessage);
		
		// - Best variance for this keyword
		$varianceItem = $this->findBestVariance($userName, $userMessage, $keywordItem);
		$this->matching = $varianceItem;
		$response = $this->pickResponse($userName, $userMessage, $varianceItem);
		
		// if ('UTF-8' != $this->config->getCharset()) {}
		$currentAnswer = new TchatMessage($response, $identity->name);
		return $currentAnswer;
	}

	public function provideMeaning(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
	{
		if (null == $currentAnswer) {
			$currentAnswer = $answer;
		}
		$message = $currentAnswer->getMessage();
		// Post traitment
		if (strstr($message, '${time}')) {
			$nowDate = new \DateTime(null, new \DateTimeZone(! isset($this->knowledges->identity->timezone) ? 'Europe/Paris' : $this->knowledges->identity->timezone));
			$message = preg_replace('!\$\{time\}!i', $nowDate->format('H\hi'), $message);
		}
		if (strstr($message, '${name}')) {
			$message = preg_replace('!\$\{name\}!i', $this->knowledges->identity->name, $message);
		}
		if (strstr($message, '${conceptorName}')) {
			$message = preg_replace('!\$\{conceptorName\}!i', $this->knowledges->identity->conceptorName, $message);
		}
		if (strstr($message, '${userName}')) {
			$message = preg_replace('!\$\{userName\}!i', $request->getName(), $message);
		}
		if (! empty($this->matching->matchingData) && count($this->matching->matchingData) > 0) {
			foreach ($this->matching->matchingData as $i => $data) {
				$data = mb_strtolower($data[0]);
				$message = preg_replace('!\$\{' . $i . '\}!i', $data, $message);
				$message = preg_replace('!\$\{' . $i . '\|clean\}!i', parserUrl($data), $message);
				$message = preg_replace('!\$\{' . $i . '\|ucfirst\}!i', ucfirst($data), $message);
			}
		}
		if (! empty($this->matching->matchingKeyword) && count($this->matching->matchingKeyword) > 0) {
			$data = mb_strtolower($this->matching->matchingKeyword[0]);
			$message = preg_replace('!\$\{keyword\}!i', $data, $message);
		}
		
		$currentAnswer->setMessage($message);
		return $currentAnswer;
	}

	public function beautifyAnswer(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
	{
		if (null == $currentAnswer) {
			$currentAnswer = $answer;
		}
		$currentAnswer->setMessage(preg_replace('!(bonjour)!i', '<strong>$1</strong>', ucfirst($currentAnswer->getMessage())));
		return $currentAnswer;
	}

	private function findBestPriorityKeyword($userName, $message)
	{
		$bestPriority = - 1;
		$matchingKeywordItem = '';
		// Loop over keywords
		foreach ($this->knowledges->keywords as $keywordItem) {
			// Better priority and matching keyword (or it is the default one)
			if ($keywordItem->priority > $bestPriority && (empty($keywordItem->keyword) || preg_match('!(?:^|\s|[_-])(' . implode('|', $keywordItem->keyword) . ')(?:$|\s|[\'_,;:-])!i', $message, $matching))) {
				$bestPriority = $keywordItem->priority;
				$matchingKeywordItem = $keywordItem;
				if (! empty($matching)) {
					array_shift($matching);
					$matchingKeywordItem->matchingKeyword = $matching;
				}
			}
		}
		return $matchingKeywordItem;
	}

	private function findBestVariance($userName, $message, $keyword)
	{
		// Verify
		if (empty($keyword)) {
			self::$logger->addError('Hum, this keyword item is kind of empty', $keyword);
			return;
		}
		
		$bestPriority = - 1;
		$matchingVarianceItem;
		// Search best variance
		if (! empty($keyword->variances)) {
			foreach ($keyword->variances as $varianceItem) {
				$varianceSize = strlen($varianceItem->variance);
				if ($varianceSize > $bestPriority && preg_match_all('!' . $varianceItem->varianceRegexable . '!is', $message, $matching)) {
					$bestPriority = $varianceSize;
					$matchingVarianceItem = $varianceItem;
					if (! empty($matching)) {
						array_shift($matching);
						$matchingVarianceItem->matchingData = $matching;
					}
				}
			}
		}
		// No variance found? Use default responses
		if (empty($matchingVarianceItem->responses)) {
			@$matchingVarianceItem->responses = $keyword->defaultResponses;
		}
		$matchingVarianceItem->matchingKeyword = @$keyword->matchingKeyword;
		return $matchingVarianceItem;
	}

	private function pickResponse($userName, $userMessage, $varianceItem)
	{
		// Verify
		if (empty($varianceItem->responses)) {
			return 'Ouch !';
		}
		
		// Select random response
		$index = mt_rand(0, count($varianceItem->responses) - 1);
		$response = $varianceItem->responses[$index];
		return $response;
	}

	public function getKnowledges()
	{
		return $this->knowledges;
	}

	public function setKnowledges($knowledges)
	{
		$this->knowledges = $knowledges;
	}

	public function getConfig()
	{
		return $this->config;
	}

	public function setConfig($config)
	{
		$this->config = $config;
	}

	public function setBrainManager($brainManager)
	{
		$this->brainManager = $brainManager;
	}
}
