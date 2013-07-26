<?php

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-07-11
 */
interface ITchatBot
{
	public function isEnabled($string);

	public function loadKnowledge();

	public function generateKnowledgeCache();

	public function generateAnswer($author, $string, $date);
}



