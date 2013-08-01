<?php

namespace Fylhan\TchatBot;

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-07-11
 * @updated 2013-08-01
 */
interface ITchatBot
{
	public function isTriggered($userMessage);

	public function generateAnswer($userName, $userMessage, $dateTime);
}



