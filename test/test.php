<?php
/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
require '../vendor/autoload.php';
require '../src/Posibrain/tools.php';

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

use Posibrain\TchatBot;

// Prepare to display discussion
header("Content-Type: text/html; charset=UTF-8");

function displayDiscussion($bot, $userName, $userMessage, $datetime)
{
	$answer = $bot->generateAnswer($userMessage, $userName, $datetime);
	echo $userName . ' : ' . $userMessage . '<br />';
	echo @$answer[1] . ' : ' . @$answer[0] . '<br /><br />';
}

// Config logger for debug
$logger = new Logger('TchatBotTester');
if (! is_dir(__DIR__ . '/../logs/')) {
	mkdir(__DIR__ . '/../logs/');
	chmod(__DIR__ . '/../logs/', '755');
}
$loggerHandler = new RotatingFileHandler(__DIR__ . '/../logs/log.log', 2, Logger::DEBUG);
$logger->pushHandler($loggerHandler);
$logger->addWarning("Launch test");

// Launch Test
$botSammy = new TchatBot('', '', array(
	'loggerHandler' => $loggerHandler
));
// $botDaneel1 = new TchatBot('daneel', 'en', array(
// 	'loggerHandler' => $loggerHandler
// ));
// $botDaneel2 = new TchatBot('daneel', 'fr', array(
// 	'loggerHandler' => $loggerHandler
// ));
// $botSammyIso = new TchatBot('sammy-iso', 'fr', array(
// 	'charset' => 'iso-8859-1',
// 	'loggerHandler' => $loggerHandler
// ));
// displayDiscussion($botSammyIso, 'Bnmaster', 'Je veux un médecin !', time());
// displayDiscussion($botDaneel1, 'Bnmaster', 'Quel est ton nom ?', time());
// displayDiscussion($botDaneel2, 'Bnmaster', 'Quel est ton nom ?', time());
// displayDiscussion($botDaneel2, 'Bnmaster', 'Ton nom est plutôt moche, non ?', time());
// displayDiscussion($botDaneel2, 'Bnmaster', 'RSammy, c\'est plutôt moche, non ?', time());
// displayDiscussion($botDaneel2, 'Bnmaster', 'Bonjour Daneel', time());
displayDiscussion($botSammy, 'Bnmaster', 'R. Sammy c\'est super laid !', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour R. Sammy', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour Sammy', time());
displayDiscussion($botSammy, 'Bnmaster', '@BN Bonjour mon ami', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour mon ami', time());
displayDiscussion($botSammy, 'Bnmaster', 'Salut l\'amie', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour Roger', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour Camarade', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour camarade', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour camarade truc', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour camarade faim', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour camarade j\'ai faim', time());
displayDiscussion($botSammy, 'Bnmaster', 'Bonjour camarade j\'ai soif', time());
displayDiscussion($botSammy, 'Bnmaster', 'Quelle heure est-il ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'Quelle heure est il ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'As-tu faim ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'Tu as faim ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'Comment s\'appelle le président du Pérou ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'Qui est le président du Pérou ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'Salut mon pote', time());
displayDiscussion($botSammy, 'Bnmaster', 'Hi my friend', time());
displayDiscussion($botSammy, 'Bnmaster', 'Hello', time());
displayDiscussion($botSammy, 'Bnmaster', 'Docteur', time());
displayDiscussion($botSammy, 'Bnmaster', 'J\'ai besoin d\'un médecin', time());
displayDiscussion($botSammy, 'Bnmaster', 'Tu fais koi ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'Tu fais quoi ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'Non, que faites-vous monseigneur ?', time());
displayDiscussion($botSammy, 'Bnmaster', 'ça va, je me dis que toi par contre, t\'es pas très évolué', time());
displayDiscussion($botSammy, 'Bnmaster', 'ça va ?', time());
displayDiscussion($botSammy, 'Bnmaster', '@BN Salut !', time());
displayDiscussion($botSammy, 'Bnmaster', 'Aurevoir', time());
