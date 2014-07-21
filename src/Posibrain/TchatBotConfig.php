<?php
namespace Posibrain;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class TchatBotConfig
{

    private static $logger = NULL;

    private $id;

    private $lang;

    private $brainsFolder;

    private $charset;

    public function __construct($id = '', $lang = '', $params = array())
    {
        // Logger
        if (NULL == self::$logger) {
            self::$logger = new Logger(__CLASS__);
            if (! empty($params) && isset($params['loggerHandler'])) {
                self::$logger->pushHandler($params['loggerHandler']);
            }
        }
        
        // Default bot configuration
        $defaultConfig = array(
            'id' => 'sammy',
            'lang' => 'fr',
            'brainsFolder' => ((! empty($params) && isset($params['brainPath'])) ? $params['brainPath'] : __DIR__ . '/../../app/brains/'),
            'charset' => 'UTF-8'
        );
        
        // Configure the robot
        $this->id = ! empty($id) ? $id : $defaultConfig['id'];
        $this->lang = ! empty($lang) ? $lang : $defaultConfig['lang'];
        $this->brainsFolder = ! empty($params['brainsFolder']) ? $params['brainsFolder'] : $defaultConfig['brainsFolder'];
        $this->brainsFolder .= ! endsWith('/', $this->brainsFolder) ? '/' : '';
        $this->setCharset(isset($params['charset']) ? $params['charset'] : $defaultConfig['charset']);
        
        $tryNumber = 0;
        // Find some knwoledge positrons for this bot
        while (! is_file($this->getKnowledgeFile())) {
            ++$tryNumber;
            // Try to change lang
            if (1 == $tryNumber) {
                if ($this->lang == $defaultConfig['lang']) {
                    continue;
                }
                $this->lang = $defaultConfig['lang'];
                $this->setCharset($defaultConfig['charset']);
                continue;
            }
            // Try to change id
            else if (2 == $tryNumber) {
                if ($this->id == $defaultConfig['id']) {
                    continue;
                }
                $this->id = $defaultConfig['id'];
                $this->lang = ! empty($lang) ? $lang : $defaultConfig['lang'];
                $this->setCharset($defaultConfig['charset']);
                continue;
            }
            // Try to change id and lang
            else if (3 == $tryNumber) {
                if ($this->id == $defaultConfig['id'] && $this->lang == $defaultConfig['lang']) {
                    continue;
                }
                $this->id = $defaultConfig['id'];
                $this->lang = $defaultConfig['lang'];
                $this->setCharset($defaultConfig['charset']);
                continue;
            }
            // Arg!
            else {
                self::$logger->addError('Definitely no such bot: this discussion is going to be very stupid!', array(
                    $id,
                    $lang,
                    $params
                ));
                $this->setCharset($defaultConfig['charset']);
                break;
            }
        }
        if ($tryNumber >= 1) {
            self::$logger->addWarning('No such bot, load crazy stupid ' . $this->id . ' (' . $this->lang . ') instead.', array(
                $id,
                $lang,
                $params
            ));
        }
    }

    public function getIdentityFile()
    {
        return $this->brainsFolder . $this->id . '/identity.json';
    }

    public function getSynonymsFile()
    {
        return $this->brainsFolder . $this->id . '/' . $this->lang . '_synonyms.json';
    }

    public function getKnowledgeFile()
    {
        return $this->brainsFolder . $this->id . '/' . $this->lang . '_knowledge.json';
    }

    public function getComputedKnowledgeFile()
    {
        return $this->brainsFolder . $this->id . '/' . $this->lang . '_knowledge_computed.json';
    }

    public function getNoKnowledgeFile()
    {
        return __DIR__ . '/brains/no-knowledge.json';
    }
    
    /* Getter / Setter */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function getBrainsFolder()
    {
        return $this->brainsFolder;
    }

    public function setBrainsFolder($brainsFolder)
    {
        $this->brainsFolder = $brainsFolder;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
        // Manage everything internally as UTF-8
        // Bot files, and bot output can have a different charset
        // Conversion is done when files are loaded
        // and when bot reply is returned
        mb_internal_encoding('UTF-8');
    }
}



