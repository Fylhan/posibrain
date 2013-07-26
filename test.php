<?php
include('TchatBot.php');

header("Content-Type: text/html; charset=UTF-8");

$bot = new TchatBot();
echo $bot->generateAnswer('Bnmaster', '@BN Bonjour mon ami', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Bonjour mon ami', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Salut l\'amie', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Bonjour Roger', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Bonjour Camarade', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Bonjour camarade', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Bonjour camarade truc', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Bonjour camarade faim', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Bonjour camarade j\'ai faim', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Bonjour camarade j\'ai soif', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Quelle heure est-il ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Quelle heure est il ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'As-tu faim ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Tu as faim ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Comment s\'appelle le président du Pérou ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Qui est le président du Pérou ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Salut mon pote', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Hi my friend', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Hello', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Docteur', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'J\'ai besoin d\'un médecin', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Tu fais koi ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Tu fais quoi ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Non, que faites-vous monseigneur ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'ça va, je me dis que toi par contre, t\'es pas très évolué', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'ça va ?', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', '@BN Salut !', time()).'<br /><br />';
echo $bot->generateAnswer('Bnmaster', 'Aurevoir', time()).'<br /><br />';
