<?php

namespace Fylhan\TchatBot;

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
	private $name;
	private $conceptorName;
	private $lang;
	private $knowledgeFile;
	private $brainsFolder;
	private $charset;


	public function __construct($params=array()) {
		if (NULL == self::$logger) {
			self::$logger = new Logger('TchatBotConfig');
			if (!is_dir(__DIR__.'/../../../logs/')) {
				mkdir(__DIR__.'/../../../logs/');
				chmod(__DIR__.'/../../../logs/', '755');
			}
			self::$logger->pushHandler(new StreamHandler(__DIR__.'/../../../logs/log.log', Logger::DEBUG));
		}

		$this->name = isset($params['name']) ? $params['name'] : 'Hari S.';
		$this->conceptorName = isset($params['conceptorName']) ? $params['conceptorName'] : 'Fylhan';
		$this->lang = isset($params['lang']) ? $params['lang'] : 'fr';
		$this->knowledgeFile = isset($params['knowledgeFile']) ? $params['knowledgeFile'] : 'knowledge.txt';
		$this->brainsFolder = isset($params['brainsFolder']) ? $params['brainsFolder'].(!endsWith('/', $params['brainsFolder']) ? '/' : '') : __DIR__.'/brains/';
		$this->charset = isset($params['charset']) ? $params['charset'] : 'UTF-8';
		
		// Config
		if ('UTF-8' == $this->charset) {
			mb_internal_encoding('UTF-8');
		}
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

	public function getBrainsFolder() {
		return $this->brainsFolder;
	}
	public function setBrainsFolder($brainsFolder) {
		$this->brainsFolder = $brainsFolder;
	}
}



