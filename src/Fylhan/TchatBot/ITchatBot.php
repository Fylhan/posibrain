<?php

namespace Fylhan\TchatBot;

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-07-11
 */
interface ITchatBot
{
	public function isTriggered($string);

	public function loadKnowledge();

	public function generateKnowledgeCache();

	public function generateAnswer($author, $string, $date);
}



