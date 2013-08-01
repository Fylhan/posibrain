<?php

namespace Fylhan\TchatBot;

use Monolog\Logger;

use Fylhan\TchatBot\BrainManager;

include_once('tools.php');


/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-07-11
 * @updated 2013-08-01
 */
class TchatBot implements ITchatBot
{
	private static $logger = NULL;
	private $config;
	private $brainManager;
	private $knowledges;


	public function __construct($params=array()) {
		// Logger
		if (NULL == self::$logger) {
			self::$logger = new Logger(__CLASS__);
			if (!empty($params) && isset($params['loggerHandler'])) {
				self::$logger->pushHandler($params['loggerHandler']);
			}
		}

		// Config
		$this->config = new TchatBotConfig($params);// TODO: DI
		
		// Brain Manager
		$this->brainManager = new BrainManager($params);// TODO: DI
		
		// Init seed for random values
		mt_srand((double)microtime()*1000000);
	}


	/**
	 * @Override
	 */
	public function isTriggered($content) {
		return (NULL != $content);
	}

	/**
	 * @Override
	 */
	public function generateAnswer($userName, $userMessage, $dateTime) {
		// Don't trigger this bot
		if (!$this->isTriggered($userMessage)) {
			return NULL;
		}
		
		// -- Load knowledge file
		if (empty($this->knowledges) && NULL == ($this->knowledges = $this->brainManager->loadBrain($this->config))) {
			return 'Rahh, someone eat my brain!';
		}
		$synonyms = $this->knowledges->synonyms;
		$knowledge = $this->knowledges->keywords;

		// -- Generate reply
		// - Check User Message
		if (empty($userMessage)){
			$message = 'Hello';
		}
		
		// - Best keyword priority
		$keywordItem = $this->findBestPriorityKeyword($userMessage);
		
		// - Best variance for this keyword
		$varianceItem = $this->findBestVariance($userMessage, $keywordItem);
		$response = $this->getResponse($varianceItem);
		
		return array($this->config->getName(), $response);
	}
	
	public function findBestPriorityKeyword($message) {
		$bestPriority = -1;
		$matchingKeywordItem = '';
		// Loop over keywords
		foreach($this->knowledges->keywords AS $keywordItem) {
			// Better priority and matching keyword (or it is the default one)
			if ($keywordItem->priority > $bestPriority
				&& (empty($keywordItem->keyword)
					|| preg_match('!(?:^|\s|[_-])('.implode('|', $keywordItem->keyword).')(?:$|\s|[\'_-])!i', $message, $matching))) {
				$bestPriority = $keywordItem->priority;
				$matchingKeywordItem = $keywordItem;
				if (!empty($matching)) {
					array_shift($matching);
					$matchingKeywordItem->matchingKeyword = $matching;
				}
			}
		}
		return $matchingKeywordItem;
	}
	
	public function findBestVariance($message, $keyword) {
		// Verify
		if (empty($keyword)) {
			logger('Error: hum, this keyword item is kind of empty', __LINE__);
			return;
		}
		
		$bestPriority = -1;
		$matchingVarianceItem;
		// Search best variance
		if (!empty($keyword->variances)) {
			foreach($keyword->variances AS $varianceItem) {
				$varianceSize = strlen($varianceItem->variance);
				if ($varianceSize > $bestPriority 
						&& preg_match_all('!'.$varianceItem->varianceRegexable.'!is', $message, $matching)) {
					$bestPriority = $varianceSize;
					$matchingVarianceItem = $varianceItem;
					if (!empty($matching)) {
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
	
	public function getResponse($varianceItem) {
		// Verify
		if (empty($varianceItem->responses)) {
			return 'Ouch !';
		}
		
		// Select random response
		$index = mt_rand(0, count($varianceItem->responses)-1);
		$response = $varianceItem->responses[$index];
		
		// Post traitment
		if (strstr($response, '${time}')) {
			$nowDate = new \DateTime(null, new \DateTimeZone('Europe/Paris'));
			$response = preg_replace('!\$\{time\}!i', $nowDate->format('H\hi'), $response);
		}
		if (!empty($varianceItem->matchingData) && count($varianceItem->matchingData) > 0) {
			foreach($varianceItem->matchingData AS $i => $data) {
				$data = mb_strtolower($data[0]);
				$response = preg_replace('!\$\{'.$i.'\}!i', $data, $response);
				$response = preg_replace('!\$\{'.$i.'\|clean\}!i', parserUrl($data), $response);
				$response = preg_replace('!\$\{'.$i.'\|ucfirst\}!i', ucfirst($data), $response);
			}
		}
		if (!empty($varianceItem->matchingKeyword) && count($varianceItem->matchingKeyword) > 0) {
			$data = mb_strtolower($varianceItem->matchingKeyword[0]);
			$response = preg_replace('!\$\{keyword\}!i', $data, $response);
		}
		return $response;
	}


	public function getConfig() {
		return $this->config;
	}
	public function setConfig($config) {
		$this->config = $config;
	}

	public function getKnowledges() {
		return $this->knowledges;
	}
	public function setKnowledges($knowledges) {
		$this->knowledges = $knowledges;
	}

	public function setBrainManager($brainManager) {
		$this->brainManager = $brainManager;
	}
}

