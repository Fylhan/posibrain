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
		
		// Init seed for random values
		mt_srand((double) microtime() * 1000000);
	}

	public function isTriggered(TchatMessage $request, $currentAnswer = true)
	{
		$content = $request->getMessage();
		$triggered = (NULL != $content);
		$identity = $this->knowledges->identity;
		// Triggered by specific rules
		if (! empty($identity->trigger)) {
			// Called by his name
			if (! empty($identity->trigger->called)) {
				$triggered &= preg_match('!(?:^|\s|[_-])(' . implode('|', $identity->trigger->called) . ')(?:$|\s|[\'_-])!i', $content);
			}
			// Specific sentance
			if (! empty($identity->trigger->sentance)) {
				$triggered &= preg_match('!(' . implode('|', $identity->trigger->sentance) . ')!i', $content);
			}
		}
		self::$logger->addInfo("Bot is triggered? ".$triggered);
		return $triggered;
	}

	public function generateSymbolicAnswer(AnalysedRequest $request, TchatMessage $currentAnswer = null)
	{
		$userMessage = $request->getMessage();
		$userName = $request->getName();
		$dateTime = $request->getDate();
		self::$logger->addInfo("generateSymbolicAnswer for request: ".$request);
		
		// -- Load knowledge file
		if (empty($this->knowledges) && NULL == ($this->knowledges = $this->brainManager->loadBrain($this->config))) {
			// Robustness, because an empty crazy knowledge should at least be available
			return array(
				'Qzhge',
				'Rahh, someone eats my brain!'
			);
		}
		$identity = $this->knowledges->identity;
		$synonyms = $this->knowledges->synonyms;
		$knowledge = $this->knowledges->keywords;
		
		// Don't trigger this bot
		if (! $this->isTriggered($userMessage)) {
			return NULL;
		}
		
		// -- Generate reply
		// - Check User Message
		if (empty($userMessage)) {
			$message = 'Hello';
		}
		
		// - Best keyword priority
		$keywordItem = $this->findBestPriorityKeyword($userName, $userMessage);
		
		// - Best variance for this keyword
		$varianceItem = $this->findBestVariance($userName, $userMessage, $keywordItem);
		$response = $this->getResponse($userName, $userMessage, $varianceItem);
		
// 		if ('UTF-8' != $this->config->getCharset()) {}
		$currentAnswer = new TchatMessage($response, $identity->name, new \DateTime());
		return $currentAnswer;
	}
	
	public function beautifyAnswer(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer=null)
	{
		if (null == $currentAnswer) {
			$currentAnswer = $answer;
		}
		$currentAnswer->setMessage(preg_replace('!(bonjour)!i', '<strong>$1</strong>', ucfirst($currentAnswer->getMessage())));
		return $currentAnswer;
	}

	public function findBestPriorityKeyword($userName, $message)
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

	public function findBestVariance($userName, $message, $keyword)
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
			$matchingVarianceItem->responses = $keyword->defaultResponses;
		}
		$matchingVarianceItem->matchingKeyword = @$keyword->matchingKeyword;
		return $matchingVarianceItem;
	}

	public function getResponse($userName, $userMessage, $varianceItem)
	{
		// Verify
		if (empty($varianceItem->responses)) {
			return 'Ouch !';
		}
		
		// Select random response
		$index = mt_rand(0, count($varianceItem->responses) - 1);
		$response = $varianceItem->responses[$index];
		
		// Post traitment
		if (strstr($response, '${time}')) {
			$nowDate = new \DateTime(null, new \DateTimeZone('Europe/Paris'));
			$response = preg_replace('!\$\{time\}!i', $nowDate->format('H\hi'), $response);
		}
		if (strstr($response, '${name}')) {
			$response = preg_replace('!\$\{name\}!i', $this->knowledges->identity->name, $response);
		}
		if (strstr($response, '${conceptorName}')) {
			$response = preg_replace('!\$\{conceptorName\}!i', $this->knowledges->identity->conceptorName, $response);
		}
		if (strstr($response, '${userName}')) {
			$response = preg_replace('!\$\{userName\}!i', $userName, $response);
		}
		if (! empty($varianceItem->matchingData) && count($varianceItem->matchingData) > 0) {
			foreach ($varianceItem->matchingData as $i => $data) {
				$data = mb_strtolower($data[0]);
				$response = preg_replace('!\$\{' . $i . '\}!i', $data, $response);
				$response = preg_replace('!\$\{' . $i . '\|clean\}!i', parserUrl($data), $response);
				$response = preg_replace('!\$\{' . $i . '\|ucfirst\}!i', ucfirst($data), $response);
			}
		}
		if (! empty($varianceItem->matchingKeyword) && count($varianceItem->matchingKeyword) > 0) {
			$data = mb_strtolower($varianceItem->matchingKeyword[0]);
			$response = preg_replace('!\$\{keyword\}!i', $data, $response);
		}
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
