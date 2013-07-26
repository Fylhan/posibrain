<?php
include('tools.php');
include_once('ITchatBot.php');

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-07-11
 */
class TchatBot implements ITchatBot
{
	private $name;
	private $conceptorName;
	private $lang;
	private $knowledgeFile;
	private $brainsFolder;
	private $knowledges;
	private $charset;


	public function __construct($params=array()) {
		$this->name = isset($params['name']) ? $params['name'] : 'Hari S.';
		$this->conceptorName = isset($params['conceptorName']) ? $params['conceptorName'] : 'Fylhan';
		$this->lang = isset($params['lang']) ? $params['lang'] : 'fr-fr';
		$this->knowledgeFile = isset($params['knowledgeFile']) ? $params['knowledgeFile'] : 'knowledge.txt';
		$this->brainsFolder = isset($params['brainsFolder']) ? $params['brainsFolder'].(!endsWith('/', $params['brainsFolder']) ? '/' : '') : 'brains/';
		$this->charset = isset($params['charset']) ? $params['charset'] : 'UTF-8';
		
		// Init seed for random values
		mt_srand((double)microtime()*1000000);
		
		// Config
		if ('UTF-8' == $this->charset) {
			mb_internal_encoding('UTF-8');
		}
	}


	public function isTriggered($content) {
		return (NULL != $content && startsWith('@Hari', $content));
	}

	public function loadKnowledge() {
		if (!is_file($this->brainsFolder.'knowledge.php')) {
			$this->generateKnowledgeCache();
		}
		// include($this->brainsFolder.'knowledge.php');
		return preg_split('!\n!', file_get_contents($this->brainsFolder.$this->getKnowledgeFile()));
	}
	
	private function loadJsonFile($filepath) {
		// Load JSON file
		$data = file_get_contents($filepath);
		// Clean
		$data = cleanJsonString($data);

		// Parse JSON
		if (NULL == ($knowledge = json_decode($data))) {
			echo getJsonLastError();
			return NULL;
		}
		return $knowledge;
	}
	public function generateKnowledgeCache() {
		// -- Load JSON knowledge
		$synonyms = $this->loadJsonFile($this->brainsFolder.'synonyms.json');
		$this->knowledge = $this->loadJsonFile($this->brainsFolder.'knowledge.json');

		// Pre-compute synonyms
		foreach($this->knowledge->keywords AS $k => $keyword) {
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
		file_put_contents($this->brainsFolder.'knowledge_computed.json', json_encode($this->knowledges));
		// echa($knowledge);
		return true;
	}
	
	private function getSynonyms($synonymKey, $synonymList) {
		foreach($synonymList AS $synonym) {
			if ($synonym->key == $synonymKey) {
				return $synonym->synonyms;
			}
		}
	}
	
	public function generateAnswerV0($author, $message, $date) {
		// setup initial variables and values
		$pvarray = 0;
		$bestvariance  = 0;
		$bestData  = 0;
		$kwarray = array();
		$vararray = array();
		$resparray = array();
		$priarray = array();
		$wordarray = array();
		$kwcount=0; $varcount=0; $respcount=0; $syncount=0;

		// load knowledge file
		$lines_array = $this->loadKnowledge();
		$count = count($lines_array);

		// This for loop goes through the entire knowledge file and places
		// the elements into arrays.  This later allows us to pull the information
		// (ie. key words, variances on the keywords, and responses) out of the
		// arrays.
		for ($x=0;$x<$count;$x++){
			$lines_array[$x] = trim($lines_array[$x]);
			$lines_array[$x] = preg_replace('!\[\]!',"",$lines_array[$x]);
		    if (strstr($lines_array[$x],"key:")){
				preg_match("!key: (.*)!i",$lines_array[$x],$kw);
				$kwarray[$kwcount] = strtoupper($kw[1]);
				$currentkw = $kwcount;
				$kwcount++;
				$varcount=0; // reset varcount to null
				$respcount=0; // reset respcount to null
				$pricount=0; // reset pricount to null
		    }
			else if (strstr($lines_array[$x],"var:")){
		       	preg_match("!var: (.*)!i",$lines_array[$x],$variance);
				$vararray[$currentkw][$varcount] = strtoupper($variance[1]);
				$varcurrent=$varcount;
				$varcount++;
				$respcount=0;
		    }
			else if (strstr($lines_array[$x],"pri:")){
				preg_match("!pri: (.*)!i",$lines_array[$x],$priority);
				$priarray[$currentkw] = $priority[1];
		    }
			else if (strstr($lines_array[$x],"resp:")){
		        preg_match("!resp: (.*)!i",$lines_array[$x],$response);
				$resparray[$currentkw][$varcurrent][$respcount] = $response[1];
				$respcount++;
		    }else if (strstr($lines_array[$x],"syn:")){
				preg_match("!syn: (.*)!i",$lines_array[$x],$synonym);
				$synonymarray[$syncount] = strtoupper($synonym[1]);
				$synonyms = explode(' ', strtoupper($synonym[1]));
				$synonymGroups[$synonyms[0]] = $synonyms;
				$syncount++;
		    }
			else if (strstr($lines_array[$x],"goto:")){
				preg_match("!goto: (.*)!i",$lines_array[$x],$goto);
				$goto = strtoupper($goto[1]);
				// find the keyword
				for ($zcount=0;$zcount<count($kwarray);$zcount++){
				   // if the keyword already exists
				   if (preg_match('!'.$goto.'!i',$kwarray[$zcount])){
					// then we assign properties of the keyword
					$vararray[$currentkw][0] = $kwarray[$currentkw];
					$resparray[$currentkw] = $resparray[$zcount];
				   }
				}
		   }
		}

		$y=0;
		$z=0;
		$v=0;
		$bestpriority=-2;
		$originalstring = $message;
		if (!$message){
		    $message = "hello";
		}
		$message = strtoupper($message);

		// Figures out what word in the string has the most priority.
		// It can then check words to the left/right of this word depending
		// upon settings in the knowledge.txt file.
		while ($y < count($kwarray)){
			// remove beginning and trailing white space, breaks, etc
			$message = trim($message);
			// remove puncuation from string
			$message = preg_replace('![\!\?,:\.]!i','',$message);
			// split the string up into seperate words   
			$wordarray = explode(' ',$message);
			while ($v < count($wordarray)) {
				if(preg_match('!'.$wordarray[$v].'$!i', $kwarray[$y])) {
					// find which word holds the most weight in the sentance
					if($bestpriority==-2){
					   $bestpriority=$y;
					}
					else if ($priarray[$bestpriority] < $priarray[$y]) {
						$bestpriority = $y;
					}
				}
				$v++;
		    }
		    $v = 0;
		    $y++;
		}

		// -- Find the variance with the most matching words
		$vcount = 0;
		while ($vcount < count($vararray[$bestpriority])) {
			$vararrayOriginal[$bestpriority][$vcount] = $vararray[$bestpriority][$vcount];
			// Pre-traitment: There are synonyms for this variance
			if (preg_match_all('!@\{([^\}]*)\}!i', $vararray[$bestpriority][$vcount], $syn)) {
				// Replace each synonym key, by the list of synonyms
				for ($synCounter = 0; $synCounter<count($syn[1]); $synCounter++) { 
					$synonym = $syn[1][$synCounter];
					$vararray[$bestpriority][$vcount] = preg_replace('!@\{'.$synonym.'\}!i', '('.implode('|', $synonymGroups[$synonym]).')', 
$vararray[$bestpriority][$vcount]);
				}
			}
			
			// Check this variance
			if (preg_match('!'.$vararray[$bestpriority][$vcount].'!i', $message, $data)) {
				// Create a variance with the selected synonym(s)
				for ($i=1; $i<count($data); $i++) {
					$selectedSynonym = strtolower($data[$i]);
					$vararrayOriginal[$bestpriority][$vcount] = preg_replace('!@\{[^\}]*\}!i', $selectedSynonym, $vararrayOriginal[
$bestpriority][$vcount], 1);
				}
				$varray = explode(' ', $vararrayOriginal[$bestpriority][$vcount]);
				$varraySize = count($varray);
				if ($varraySize > $pvarray) {
					$bestData = $data;
					$bestvariance = $vcount;
					$pvarray = $varraySize;
				}
			}
			$vcount++;
		}

		// Using the bestpriority (aka the keyword (key:) with the most weight in the sentence)
		// and the bestvariance (aka, the variance (var:) phrase that most fits the context of
		// the original sentence, we form a response.
		if (count($resparray[$bestpriority][$bestvariance]) > 1){
			$random = mt_rand(0,count($resparray[$bestpriority][$bestvariance])-1);
		}else{
			$random = 0;
		}
		$response = $resparray[$bestpriority][$bestvariance][$random];
		if ('' == $response){
			$response = 'Désolé, mais je ne comprends pas ce que tu dis.';
		}
		
		// -- Post traitment
		if (strstr($response, '${time}')) {
			$nowDate = new DateTime(null, new DateTimeZone('Europe/Paris'));
			$response = preg_replace('!\$\{time\}!i', $nowDate->format('H\hi'), $response);
		}
		if (NULL != $bestData && count($bestData) > 0) {
			foreach($bestData AS $i => $result) {
				$result = strtolower($result);
				$response = preg_replace('!\$\{'.$i.'\}!i', $result, $response);
				$response = preg_replace('!\$\{'.$i.'\|clean\}!i', parserUrl($result), $response);
			}
		}

		// $originalstring = preg_replace('!\[\]!',"",$originalstring);
		// $originalMessage = new Message("Me", $originalstring);
		// $originalMessage->toString();
		// return new Message(self::$botName, $response);
		return $response;
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
			$nowDate = new DateTime(null, new DateTimeZone('Europe/Paris'));
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
		return $response;
	}
	
	public function generateAnswer($author, $message, $date) {
		// Load knowledge file
		$this->loadKnowledge();
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
		
		echo 'Me: '.$message.'<br />';
		// echo 'ResponseV0: '.$this->generateAnswerV0($author, $message, $date).'<br />';
		echo 'Response: ';
		return $response;
	}
	

	public function getLang() {
		return $this->lang;
	}
	public function setLang($lang) {
		$this->lang = $lang;
	}

	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}

	public function getConceptorName() {
		return $this->conceptorName;
	}
	public function setConceptorName($conceptorName) {
		$this->conceptorName = $conceptorName;
	}

	public function getKnowledgeFile() {
		// TODO: manage several language
		return $this->knowledgeFile;
	}
	public function setKnowledgeFile($knowledgeFile) {
		$this->knowledgeFile = $knowledgeFile;
	}
}



