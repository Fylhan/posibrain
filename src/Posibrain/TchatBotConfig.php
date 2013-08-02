<?php

namespace Posibrain;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-07-31
 * @updated 2013-07-31
 */
class TchatBotConfig
{
	private static $logger = NULL;
	private $id;
	private $lang;
	private $brainsFolder;
	private $charset;


	public function __construct($id='', $lang='', $params=array()) {
		// Logger
		if (NULL == self::$logger) {
			self::$logger = new Logger(__CLASS__);
			if (!empty($params) && isset($params['loggerHandler'])) {
				self::$logger->pushHandler($params['loggerHandler']);
			}
		}

		// Configure the robot
		$this->id = $id;
		$this->lang = $lang;
		$this->brainsFolder = @$params['brainsFolder'].(!endsWith('/', @$params['brainsFolder']) ? '/' : '');
		$this->setCharset(isset($params['charset']) ? $params['charset'] : 'UTF-8');
		
		if (!is_file($this->getKnowledgeFile())) {
			self::$logger->addError('No such bot: load crazy stupid R. Sammy', array($id, $lang, $params));
			$this->id = 'sammy';
			$this->lang = 'fr';
			$this->brainsFolder = __DIR__.'/brains/';
		}
	}


	public function getIdentityFile() {
		return $this->brainsFolder.$this->id.'/identity.json';
	}
	
	public function getSynonymsFile() {
		return $this->brainsFolder.$this->id.'/'.$this->lang.'_synonyms.json';
	}
		
	public function getKnowledgeFile() {
		return $this->brainsFolder.$this->id.'/'.$this->lang.'_knowledge.json';
	}
	
	public function getComputedKnowledgeFile() {
		return $this->brainsFolder.$this->id.'/'.$this->lang.'_knowledge_computed.json';
	}
	

	/* Getter / Setter */
	public function getId() {
		return $this->name;
	}
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getLang() {
		return $this->lang;
	}
	public function setLang($lang) {
		$this->lang = $lang;
	}

	public function getBrainsFolder() {
		return $this->brainsFolder;
	}
	public function setBrainsFolder($brainsFolder) {
		$this->brainsFolder = $brainsFolder;
	}
	
	public function getCharset() {
		return $this->charset;
	}
	public function setCharset($charset) {
		$this->charset = $charset;
		if ('UTF-8' == $this->charset) {
			mb_internal_encoding('UTF-8');
		}
	}
}



