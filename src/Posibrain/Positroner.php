<?php
namespace Posibrain;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use Monolog\Logger;
include_once (__DIR__ . '/../tools.php');

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class Positroner implements IPositroner
{

    private static $logger = NULL;

    public $config;

    public $params;

    public $positrons;

    public $selectedPositrons;

    public function __construct($config = array(), $params = array())
    {
        // Logger
        if (NULL == self::$logger) {
            self::$logger = new Logger(__CLASS__);
            if (! empty($params) && isset($params['loggerHandler'])) {
                self::$logger->pushHandler($params['loggerHandler']);
            }
        }
        
        // Init seed for random values
        mt_srand((double) microtime() * 1000000);
        $this->params = $params;
        $this->config = $config;
    }

    public function listPositrons($ids = null)
    {
        $files = glob(dirname(__FILE__) . '/Positron/*/*Positron.php');
        if (empty($files))
            $files = array();
        
        $positrons = array();
        foreach ($files as $file) {
            $path = preg_replace('!^.*(Posibrain/Positron/.*/.*Positron).*$!ui', '$1', $file);
            $name = preg_replace('!^.*Posibrain/Positron/(.*)/.*Positron.*$!ui', '$1', $file);
            if (empty($ids) || $ids === $name  || (is_array($ids) && in_array($name, $ids)))  
            {
                $positrons[$name] = $path;
            }
        }
        return $positrons;
    }

    public function loadPositrons($ids, $config, $params = array())
    {
        $positrons = $this->listPositrons($ids);
        foreach ($positrons as $positronName => $positronPath) {
            require_once (dirname(__FILE__) . '/../' . $positronPath . '.php');
            $className = '\\' . str_replace('/', '\\', $positronPath);
            $positron = new $className($config, $params);
            $this->positrons[$positronName] = $positron;
        }
        $this->selectedPositrons = array();
        return $this->positrons;
    }

    public function getPostitrons()
    {
        if (empty($this->positrons))
            $this->loadPositrons($this->config, $this->params);
        return $this->positrons;
    }

    public function findPositron($id)
    {
        if (! isset($this->positrons[$id])) {
            return null;
        }
        return $this->positrons[$id];
    }

    public function updatePositron($id, $state)
    {}

    public function isBotTriggered(TchatMessage $request, $currentValue = true)
    {
        if (empty($this->positrons)) {
            return $currentValue;
        }
        // Positrons trigerred?
        $this->selectedPositrons = array();
        foreach ($this->positrons as $positron) {
            if ($positron->isPositronTriggered($request)) {
                $this->selectedPositrons[] = $positron;
            }
        }
        // Bot trigerred?
        foreach ($this->selectedPositrons as $positron) {
            $currentValue = $positron->isBotTriggered($request, $currentValue);
        }
        return $currentValue;
    }

    public function analyseRequest(TchatMessage $request, AnalysedRequest $currentAnalysedRequest = null)
    {
        if (empty($this->selectedPositrons)) {
            return new AnalysedRequest($request->getMessage(), $request->getName(), $request->getDate(), $request);
        }
        foreach ($this->selectedPositrons as $positron) {
            $currentAnalysedRequest = $positron->analyseRequest($request, $currentAnalysedRequest);
        }
        if (null == $currentAnalysedRequest) {
            $currentAnalysedRequest = new AnalysedRequest($request->getMessage(), $request->getName(), $request->getDate(), $request);
        }
        return $currentAnalysedRequest;
    }

    public function loadMemory(AnalysedRequest $request, $currentMemory = null)
    {
        if (empty($this->selectedPositrons)) {
            return $currentMemory;
        }
        foreach ($this->selectedPositrons as $positron) {
            $currentMemory = $positron->loadMemory($request, $currentMemory);
        }
        return $currentMemory;
    }

    public function generateSymbolicAnswer(AnalysedRequest $request, $memory = null, TchatMessage $currentAnswer = null)
    {
        if (empty($this->selectedPositrons)) {
            return new TchatMessage('Goxjjdp !?');
        }
        foreach ($this->selectedPositrons as $positron) {
            $currentAnswser = $positron->generateSymbolicAnswer($request, $memory, $currentAnswer);
        }
        if (null == $currentAnswser) {
            $currentAnswser = new TchatMessage('HJJoc ?');
        }
        return $currentAnswser;
    }

    public function provideMeaning(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
    {
        if (empty($this->selectedPositrons)) {
            return new TchatMessage('Ndfdslp !?');
        }
        foreach ($this->selectedPositrons as $positron) {
            $currentAnswser = $positron->provideMeaning($request, $memory, $answer, $currentAnswer);
        }
        if (null == $currentAnswser) {
            $currentAnswser = new TchatMessage('Ndfdslp ?');
        }
        return $currentAnswser;
    }

    public function beautifyAnswer(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
    {
        if (empty($this->selectedPositrons)) {
            return $currentAnswer;
        }
        foreach ($this->selectedPositrons as $positron) {
            $currentAnswser = $positron->beautifyAnswer($request, $memory, $answer, $currentAnswer);
        }
        return $currentAnswser;
    }
}