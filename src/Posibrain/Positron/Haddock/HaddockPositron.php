<?php
namespace Posibrain\Positron\Haddock;

use Monolog\Logger;
use Posibrain\Positron\Positron;
use Posibrain\TchatBotConfig;
use Posibrain\AnalysedRequest;
use Posibrain\TchatMessage;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class HaddockPositron extends Positron
{

    private static $logger = NULL;

    private $config;

    private $insults;

    private $nbOfInsults;

    private $ponctuations;

    private $nbOfPonctuations;

    private $internalPonctuations;

    private $nbOfInternalPonctuations;

    public function __construct($config, $params = array())
    {
        // Logger
        if (NULL == self::$logger) {
            self::$logger = new Logger(__CLASS__);
            if (! empty($params) && isset($params['loggerHandler'])) {
                self::$logger->pushHandler($params['loggerHandler']);
            }
        }
        
        // Brain Manager
        $this->config = $config;
        $this->insults = array(
            'Bachi-bouzouk',
            'Tonnerre de Brest',
            'Mille tonnerres de Brest',
            'Misérable ectoplasme',
            'Papou des Carpathes',
            'Que le grand cric me croque et me fasse avaler ma barbe',
            'Ectoplasme à roulettes',
            'Espèce de zouave interplanétaire',
            'Sale vilaine bête de tonnerre de Brest',
            'Espèce de porc-épic mal embouché',
            'Patagon de zoulou',
            'Loup-garou à la graisse de renoncule',
            'Bougres d’extrait de crétins des Alpes',
            'Macchabée d\'eau de vaisselle',
            'Astronaute d\'eau douce',
            'Moules à gaufres',
            'Ornithorynque',
            'Macrocéphale',
            'Iconoclaste',
            'Mille millions de mille sabords'
        );
        $this->nbOfInsults = count($this->insults);
        $this->ponctuations = array(
            '!'
        );
        $this->nbOfPonctuations = count($this->ponctuations);
        $this->internalPonctuations = array(
            '?',
            '&',
            '#',
            'ټ',
            '☠',
            '@'
        );
        $this->nbOfInternalPonctuations = count($this->internalPonctuations);
    }

    public function isPositronTriggered(TchatMessage $request)
    {
        return mt_rand(0, 1);
    }

    public function beautifyAnswer(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
    {
        if (null == $currentAnswer) {
            $currentAnswer = $answer;
        }
        
        $ponctuationSize = mt_rand(1, 3);
        $ponctuation = '';
        for ($i = 0; $i < $ponctuationSize; $i ++) {
            if ($i == ($ponctuationSize - 1) && $ponctuationSize >= 3) {
                $internalPonctuationSize = mt_rand(1, 5);
                for ($j = 0; $j < $internalPonctuationSize; $j ++) {
                    $ponctuation .= $this->internalPonctuations[mt_rand(0, $this->nbOfInternalPonctuations - 1)];
                }
            }
            $ponctuation .= $this->ponctuations[mt_rand(0, $this->nbOfPonctuations - 1)];
        }
        $insult = $this->insults[mt_rand(0, $this->nbOfInsults - 1)] . ' ' . $ponctuation;
        // Start with insulte
        if (mt_rand(0, 1)) {
            $message = $insult . ' ' . ucfirst($currentAnswer->getMessage());
        }
        // End with insult
        else {
            // Add final punctuation if not yet there
            if (! preg_match('![\.\!\?;]$!', $currentAnswer->getMessage())) {
                $currentAnswer->setMessage($currentAnswer->getMessage() . ' !');
            }
            $message = ucfirst($currentAnswer->getMessage()) . ' ' . $insult;
        }
        $currentAnswer->setMessage($message);
        return $currentAnswer;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }
}
