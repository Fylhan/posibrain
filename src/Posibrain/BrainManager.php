<?php

namespace Posibrain;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

use Monolog\Logger;

include_once('tools.php');


/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-08-01
 * @updated 2013-08-23
 */
class BrainManager implements IBrainManager
{
	private static $logger = NULL;
	
	public function __construct($params=array()) {
		// Logger
		if (NULL == self::$logger) {
			self::$logger = new Logger(__CLASS__);
			if (!empty($params) && isset($params['loggerHandler'])) {
				self::$logger->pushHandler($params['loggerHandler']);
			}
		}
	}
	
	
	/**
	 * @Override
	 */
	public function loadBrain($config) {
		if ('dev' == MODE || !is_file($config->getComputedKnowledgeFile())) {
			if (!$this->generateKnowledgeCache($config)) {
				self::$logger->addWarning('Can\'t load knowledge', $config);
				$knowledges = $this->loadJsonFile($config->getNoKnowledgeFile());
				return $knowledges;
			}
		}
		$knowledges = $this->loadJsonFile($config->getComputedKnowledgeFile());
		return $knowledges;
	}
	
	/**
	 * Generate a pre-configured JSON file with already regexable string
	 *
	 * Thanks to this benchmark: http://techblog.procurios.nl/k/news/view/34972/14863/cache-a-large-array-json-serialize-or-var_export.html
	 * I now know that JSON and serialization are similar in terms of speed performance, and I prefer JSON here
	 * We may use var_exports (cost of an include until it is created), but it didn't work, and this is not quicker apparently
	 */
	public function generateKnowledgeCache($config) {
		// -- Load JSON knowledge
		$identity = $this->loadJsonFile($config->getIdentityFile());
		$synonyms = $this->loadJsonFile($config->getSynonymsFile());
		$knowledge = $this->loadJsonFile($config->getKnowledgeFile());

		if (NULL == $identity || NULL == $synonyms || NULL == $knowledge) {
			return false;
		}
		
		foreach($knowledge->keywords AS $k => $keyword) {
			// Pre-compute keyword
			$keyword->keyword = preg_replace('!\$\{name\}!U', $identity->name, $keyword->keyword);
			$keyword->keyword = preg_replace('!\$\{conceptorName\}!U', $identity->conceptorName, $keyword->keyword);
			// Pre-compute synonyms
			if (!empty($keyword->variances)) {
				$keywordSynonyms = $keyword->keyword;
				$variances = $keyword->variances;
				$size = count($variances);
				for($i=0; $i<$size; $i++) {
					$keyword->variances[$i]->varianceRegexable = preg_replace('!\$\{keyword\}!U', '('.implode('|', $keywordSynonyms).')', $variances[$i]->variance);
					$keyword->variances[$i]->varianceRegexable = preg_replace('!\$\{name\}!U', $identity->name, $variances[$i]->varianceRegexable);
					$keyword->variances[$i]->varianceRegexable = preg_replace('!\$\{conceptorName\}!U', $identity->conceptorName, $variances[$i]->varianceRegexable);
					preg_match_all('!@\{([^\}]+)\}!U', $keyword->variances[$i]->varianceRegexable, $matchingSynonyms);
					if (NULL != $matchingSynonyms && !empty($matchingSynonyms) && !empty($matchingSynonyms[1])) {
						foreach($matchingSynonyms[1] AS $synonym) {
							$keyword->variances[$i]->varianceRegexable = preg_replace('!@\{'.$synonym.'\}!U', '('.implode('|', $this->
getSynonyms($synonym, $synonyms->synonyms)).')', $keyword->variances[$i]->varianceRegexable);
						}
					}
				}
			}
			$knowledges->keywords[$k] = $keyword;
		}
		$knowledges->identity = $identity;
		$knowledges->synonyms = $synonyms;
		// Store JSON cache
		file_put_contents($config->getComputedKnowledgeFile(), json_encode($knowledges));
		return true;
	}
	
	private function loadJsonFile($filepath) {
		// Load JSON file
		$data = @file_get_contents($filepath);
		if (false === $data) {
			self::$logger->addWarning('Can\'t load JSON file "'.$filepath.'"');
			return NULL;
		}
		// Clean
		$data = cleanJsonString($data);

		// Parse JSON
		try {
			$parser = new JsonParser();
			$knowledge = $parser->parse($data, JsonParser::ALLOW_DUPLICATE_KEYS);
		}
		catch(ParsingException $e) {
			self::$logger->addWarning('Can\'t parse JSON file "'.$filepath.'": '.$e->getMessage());
			return NULL;
		}
		return $knowledge;
	}
	
	private function getSynonyms($synonymKey, $synonymList) {
		foreach($synonymList AS $synonym) {
			if ($synonym->key == $synonymKey) {
				return $synonym->synonyms;
			}
		}
	}
}