<?php

namespace Fylhan\TchatBot;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

include_once('tools.php');


/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-07-11
 * @updated 2013-07-31
 */
class TchatBot implements ITchatBot
{
	private static $logger = NULL;
	private $config;
	private $knowledges;


	public function __construct($params=array()) {
		if (NULL == self::$logger) {
			self::$logger = new Logger('TchatBot');
			if (!is_dir(__DIR__.'/../../../logs/')) {
				mkdir(__DIR__.'/../../../logs/');
				chmod(__DIR__.'/../../../logs/', '755');
			}
			self::$logger->pushHandler(new StreamHandler(__DIR__.'/../../../logs/log.log', Logger::DEBUG));
		}

		$this->config = new TchatBotConfig($params);
		
		// Init seed for random values
		mt_srand((double)microtime()*1000000);
	}


	public function isTriggered($content) {
		return (NULL != $content && startsWith('@Hari', $content));
	}

	public function loadKnowledge() {
		self::$logger->addDebug(__FUNCTION__);
		if (!is_file($this->config->getBrainsFolder().'knowledge.php')) {
			if (!$this->generateKnowledgeCache()) {
				self::$logger->addWarning('Can\'t load knowledge');
				return NULL;
			}
		}
		// include($this->brainsFolder.'knowledge.php');
		return preg_split('!\n!', file_get_contents($this->config->getBrainsFolder().$this->config->getKnowledgeFile()));
	}
	
	private function loadJsonFile($filepath) {
		// Load JSON file
		$data = file_get_contents($filepath);
		// Clean
		$data = cleanJsonString($data);

		// Parse JSON
		try {
			$parser = new JsonParser();
			$knowledge = $parser->parse($data, JsonParser::ALLOW_DUPLICATE_KEYS);
		}
		catch(ParsingException $e) {
			self::$logger->addWarning('Can\'t load JSON file "'.$filepath.'": '.$e->getMessage());
			return NULL;
		}
		return $knowledge;
	}
	public function generateKnowledgeCache() {
		// -- Load JSON knowledge
		$synonyms = $this->loadJsonFile($this->config->getBrainsFolder().'synonyms.json');
		$knowledge = $this->loadJsonFile($this->config->getBrainsFolder().'knowledge.json');

		if (NULL == $synonyms || NULL == $knowledge) {
			return false;
		}
		
		// Pre-compute synonyms
		foreach($knowledge->keywords AS $k => $keyword) {
			if (!empty($keyword->variances)) {
				$keywordSynonyms = $keyword->keyword;
				$variances = $keyword->variances;
				$size = count($variances);
				for($i=0; $i<$size; $i++) {
					$keyword->variances[$i]->varianceRegexable = preg_replace('!\$\{keyword\}!U', '('.implode('|', $keywordSynonyms).')', 
	$variances[$i]->variance);
					preg_match_all('!@\{([^\}]+)\}!U', $keyword->variances[$i]->varianceRegexable, $matchingSynonyms);
					if (NULL != $matchingSynonyms && !empty($matchingSynonyms) && !empty($matchingSynonyms[1])) {
						foreach($matchingSynonyms[1] AS $synonym) {
							$keyword->variances[$i]->varianceRegexable = preg_replace('!@\{'.$synonym.'\}!U', '('.implode('|', $this->
getSynonyms(
	$synonym, $synonyms->synonyms)).')', $keyword->variances[$i]->varianceRegexable);
						}
					}
				}
			}
			$this->knowledges->keywords[$k] = $keyword;
		}
		$this->knowledges->synonyms = $synonyms;
		file_put_contents($this->config->getBrainsFolder().'knowledge_computed.json', json_encode($this->knowledges));
		return true;
	}
	
	private function getSynonyms($synonymKey, $synonymList) {
		foreach($synonymList AS $synonym) {
			if ($synonym->key == $synonymKey) {
				return $synonym->synonyms;
			}
		}
	}
	
	public function findBestPriorityKeyword($message) {
		$bestPriority = -2;
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
		
		$bestPriority = -2;
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
	
	public function generateAnswer($author, $message, $date) {
		// Load knowledge file
		if (empty($this->knowledges) && NULL == $this->loadKnowledge()) {
			return 'Rahh, someone eat my brain!';
		}
		$synonyms = $this->knowledges->synonyms;
		$knowledge = $this->knowledges->keywords;

		$bestpriority=-2;
		if (empty($message)){
			$message = 'Hello';
		}
		//$message = strtoupper($message);

		// -- Best keyword priority
		$keywordItem = $this->findBestPriorityKeyword($message);
		
		// -- Best variance for this keyword
		$varianceItem = $this->findBestVariance($message, $keywordItem);
		$response = $this->getResponse($varianceItem);
		
		return array($this->config->getName(), $response);
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
}

