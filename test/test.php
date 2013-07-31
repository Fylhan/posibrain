<?php

require '../vendor/autoload.php';

use Fylhan\TchatBot\TchatBot;

header("Content-Type: text/html; charset=UTF-8");

function displayDiscussion($bot, $userName, $userMessage, $datetime) {
	list($botName, $botMessage) = $bot->generateAnswer($userName, $userMessage, $datetime);
	echo $userName.' : '.$userMessage.'<br />';
	echo $botName.' : '.$botMessage.'<br /><br />';
}

$bot = new TchatBot();
displayDiscussion($bot, 'Bnmaster', '@BN Bonjour mon ami', time());
displayDiscussion($bot, 'Bnmaster', 'Bonjour mon ami', time());
displayDiscussion($bot, 'Bnmaster', 'Salut l\'amie', time());
displayDiscussion($bot, 'Bnmaster', 'Bonjour Roger', time());
displayDiscussion($bot, 'Bnmaster', 'Bonjour Camarade', time());
displayDiscussion($bot, 'Bnmaster', 'Bonjour camarade', time());
displayDiscussion($bot, 'Bnmaster', 'Bonjour camarade truc', time());
displayDiscussion($bot, 'Bnmaster', 'Bonjour camarade faim', time());
displayDiscussion($bot, 'Bnmaster', 'Bonjour camarade j\'ai faim', time());
displayDiscussion($bot, 'Bnmaster', 'Bonjour camarade j\'ai soif', time());
displayDiscussion($bot, 'Bnmaster', 'Quelle heure est-il ?', time());
displayDiscussion($bot, 'Bnmaster', 'Quelle heure est il ?', time());
displayDiscussion($bot, 'Bnmaster', 'As-tu faim ?', time());
displayDiscussion($bot, 'Bnmaster', 'Tu as faim ?', time());
displayDiscussion($bot, 'Bnmaster', 'Comment s\'appelle le président du Pérou ?', time());
displayDiscussion($bot, 'Bnmaster', 'Qui est le président du Pérou ?', time());
displayDiscussion($bot, 'Bnmaster', 'Salut mon pote', time());
displayDiscussion($bot, 'Bnmaster', 'Hi my friend', time());
displayDiscussion($bot, 'Bnmaster', 'Hello', time());
displayDiscussion($bot, 'Bnmaster', 'Docteur', time());
displayDiscussion($bot, 'Bnmaster', 'J\'ai besoin d\'un médecin', time());
displayDiscussion($bot, 'Bnmaster', 'Tu fais koi ?', time());
displayDiscussion($bot, 'Bnmaster', 'Tu fais quoi ?', time());
displayDiscussion($bot, 'Bnmaster', 'Non, que faites-vous monseigneur ?', time());
displayDiscussion($bot, 'Bnmaster', 'ça va, je me dis que toi par contre, t\'es pas très évolué', time());
displayDiscussion($bot, 'Bnmaster', 'ça va ?', time());
displayDiscussion($bot, 'Bnmaster', '@BN Salut !', time());
displayDiscussion($bot, 'Bnmaster', 'Aurevoir', time());
