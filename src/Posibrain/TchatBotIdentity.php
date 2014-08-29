<?php
namespace Posibrain;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Posibrain\OutputDecorationEnum;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class TchatBotIdentity
{

    private static $logger = NULL;

    private $id;

    private $lang;

    private $name;

    private $pseudo;

    private $avatar;

    private $conceptorName;

    /**
     *
     * @var \DateTime
     */
    private $birthday;

    /**
     *
     * @var \DateTimeZone
     */
    private $timezone;

    private $charset;

    /**
     *
     * @see \Posibrain\OutputDecorationEnum
     */
    private $outputDecoration;

    private $triggers;

    private $positrons;

    private $instinctPath;
    private $memoryPath;

    public function __construct($id = '', $lang = '', $params = array())
    {
        // Logger
        if (NULL == self::$logger) {
            self::$logger = new Logger(__CLASS__);
            if (! empty($params) && isset($params['loggerHandler'])) {
                self::$logger->pushHandler($params['loggerHandler']);
            }
        }
        
        if (key_exists('instinctPath', $params)) {
            $this->instinctPath = $params['instinctPath'];
        }
        if (key_exists('memoryPath', $params)) {
            $this->memoryPath = $params['memoryPath'];
        }
        
        // Default bot configuration
        $defaultConfig = array(
            'id' => 'sammy',
            'lang' => 'fr',
            'instinctPath' => ((! empty($params) && isset($params['instinctPath'])) ? $params['instinctPath'] : __DIR__ . '/../../app/brains/'),
            'memoryPath' => ((! empty($params) && isset($params['memoryPath'])) ? $params['memoryPath'] : __DIR__ . '/../../app/memories/'),
            'charset' => 'UTF-8'
        );
        
        // Configure the bot
        $this->id = ! empty($id) ? $id : $defaultConfig['id'];
        $this->lang = ! empty($lang) ? $lang : $defaultConfig['lang'];
        $this->instinctPath = ! empty($params['instinctPath']) ? $params['instinctPath'] : $defaultConfig['instinctPath'];
        $this->instinctPath .= ! endsWith('/', $this->instinctPath) ? '/' : '';
        $this->memoryPath = ! empty($params['memoryPath']) ? $params['memoryPath'] : $defaultConfig['memoryPath'];
        $this->memoryPath .= ! endsWith('/', $this->memoryPath) ? '/' : '';
        $this->setCharset(isset($params['charset']) ? $params['charset'] : $defaultConfig['charset']);
        $this->birthday = new \DateTime();
        $this->outputDecoration = OutputDecorationEnum::Html;
        
        if (! $this->loadIdentity($this->getIdentityPath())) {
            self::$logger->addAlert('No such bot: this conversation will be very stupid...', array(
                $id,
                $lang,
                $params
            ));
        }
        
        if (key_exists('name', $params)) {
            $this->name = $params['name'];
        }
        if (key_exists('pseudo', $params)) {
            $this->pseudo = $params['pseudo'];
        }
        if (key_exists('avatar', $params)) {
            $this->avatar = $params['avatar'];
        }
        if (key_exists('conceptorName', $params)) {
            $this->conceptorName = $params['conceptorName'];
        }
        if (key_exists('birthday', $params)) {
            $this->setBirthday($params['birthday']);
        }
        if (key_exists('timezone', $params)) {
            $this->setTimezone($params['timezone']);
        }
        if (key_exists('charset', $params)) {
            $this->charset = $params['charset'];
        }
        if (key_exists('outputDecoration', $params)) {
            $this->outputDecoration = $params['outputDecoration'];
        }
        if (key_exists('triggers', $params)) {
            $this->triggers = $params['triggers'];
        }
        if (key_exists('positrons', $params)) {
            $this->positrons = $params['positrons'];
        }
    }

    public function getIdentityPath()
    {
        return $this->instinctPath . $this->id . '/identity.json';
    }

    private function loadIdentity($identityPath)
    {
        if (! is_file($identityPath)) {
            return false;
        }
        
        $identity = loadJsonFile($identityPath);
        if (key_exists('name', $identity)) {
            $this->name = $identity->name;
        }
        if (key_exists('pseudo', $identity)) {
            $this->pseudo = $identity->pseudo;
        }
        if (key_exists('avatar', $identity)) {
            $this->avatar = $identity->avatar;
        }
        if (key_exists('conceptorName', $identity)) {
            $this->conceptorName = $identity->conceptorName;
        }
        if (key_exists('birthday', $identity)) {
            $this->setBirthday($identity->birthday);
        }
        if (key_exists('timezone', $identity)) {
            $this->setTimezone($identity->timezone);
        }
        if (key_exists('charset', $identity)) {
            $this->charset = $identity->charset;
        }
        if (key_exists('outputDecoration', $identity)) {
            $this->outputDecoration = $identity->outputDecoration;
        }
        if (key_exists('triggers', $identity)) {
            $this->triggers = $identity->triggers;
        }
        if (key_exists('positrons', $identity)) {
            $this->positrons = $identity->positrons;
        }
        
        return true;
    }

    public function getSynonymsPath()
    {
        return $this->instinctPath . $this->id . '/' . $this->lang . '_synonyms.json';
    }

    public function getKnowledgePath()
    {
        return $this->instinctPath . $this->id . '/' . $this->lang . '_knowledge.json';
    }

    public function getComputedKnowledgePath()
    {
        return $this->memoryPath . $this->id . '/' . $this->lang . '_knowledge_computed.json';
    }

    public function getNoKnowledgePath()
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

    public function getInstinctPath()
    {
        return $this->instinctPath;
    }

    public function setInstinctPath($instinctPath)
    {
        $this->instinctPath = $instinctPath;
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getPseudo()
    {
        return $this->pseudo;
    }

    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getConceptorName()
    {
        return $this->conceptorName;
    }

    public function setConceptorName($conceptorName)
    {
        $this->conceptorName = $conceptorName;
        return $this;
    }

    /**
     *
     * @return DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday($birthday)
    {
        if (! ($birthday instanceof \DateTime)) {
            $birthday = \DateTime::createFromFormat('Y-m-d', $birthday);
        }
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * 
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        if (! ($timezone instanceof \DateTimeZone)) {
            try {
                $timezone = new \DateTimeZone($timezone);
            } catch (Exception $e) {
                $timezone = new \DateTimeZone('Europe/Paris');
            }
        }
        $this->timezone = $timezone;
        return $this;
    }

    public function getOutputDecoration()
    {
        return $this->outputDecoration;
    }

    public function setOutputDecoration($outputDecoration)
    {
        $this->outputDecoration = $outputDecoration;
        return $this;
    }

    public function getTriggers()
    {
        return $this->triggers;
    }

    public function setTriggers($triggers)
    {
        $this->triggers = $triggers;
        return $this;
    }

    public function getPositrons()
    {
        return $this->positrons;
    }

    public function setPositrons($positrons)
    {
        $this->positrons = $positrons;
        return $this;
    }

    public function getMemoryPath()
    {
        return $this->memoryPath;
    }

    public function setMemoryPath($memoryPath)
    {
        $this->memoryPath = $memoryPath;
        return $this;
    }
	
}



