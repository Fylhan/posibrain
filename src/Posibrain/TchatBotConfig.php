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

		// Default bot configuration
		$defaultConfig = array('id' => 'sammy',
							'lang' => 'fr',
							'brainsFolder' => __DIR__.'/brains/',
							'charset' => 'UTF-8',
							);
		
		// Configure the robot
		$this->id = !empty($id) ? $id : $defaultConfig['id'];
		$this->lang = !empty($lang) ? $lang : $defaultConfig['lang'];
		$this->brainsFolder = !empty($params['brainsFolder'])  ? $params['brainsFolder'] : $defaultConfig['brainsFolder'];
		$this->brainsFolder .= !endsWith('/', $this->brainsFolder) ? '/' : '';
		$this->setCharset(isset($params['charset']) ? $params['charset'] : $defaultConfig['charset']);

		$tryNumber = 0;
		while (!is_file($this->getKnowledgeFile())) {
			$tryNumber++;
			// Try to change ID
			if (1 == $tryNumber) {
				if ($this->id == $defaultConfig['id']) {
					$tryNumber++;
				}
				$this->id = $defaultConfig['id'];
				self::$logger->addWarning('No such bot: load crazy stupid '.$this->id.' ('.$this->lang.')', array($id, $lang, $params));
				continue;
			}
			// Try to change lang
			if (2 == $tryNumber) {
				if ($this->lang == $defaultConfig['lang']) {
					$tryNumber++;
				}
				$this->lang = $defaultConfig['lang'];
				self::$logger->addWarning('Still no such bot: load crazy stupid '.$this->id.' ('.$this->lang.')', array($id, $lang, $params));
				continue;
			}
			// Try to change the folder
			if (3 == $tryNumber) {
				if ($this->brainsFolder == $defaultConfig['brainsFolder']) {
					$tryNumber++;
				}
				$this->brainsFolder = $defaultConfig['brainsFolder'];
				continue;
			}
			// Arg!
			else {
				self::$logger->addError('Definitely no bot: this tchat is going to be very stupid!', array($id, $lang, $params));
				break;
			}
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
	
	public function getNoKnowledgeFile() {
		return __DIR__.'/brains/no-knowledge.json';
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



