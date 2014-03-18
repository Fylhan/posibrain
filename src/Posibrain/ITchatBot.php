<?php

namespace Posibrain;

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-07-11
 * @updated 2013-08-01
 */
interface ITchatBot
{
	/**
	 * To know if the bot should be triggered
	 * @param string $userName Name of the user who speak to the bot
	 * @param string $userMessage Message of the user
	 * @param long $dateTime Date when the message had been posted
	 * @return boolean
	 */
	public function isTriggered($userMessage, $userName='', $dateTime='');

	/**
	 * Generate to the user sentence
	 * @param string $userName Name of the user who speak to the bot
	 * @param string $userMessage Message of the user
	 * @param long $dateTime Date when the message had been posted
	 * @return array($botName, $botMessage)
	 */
	public function generateAnswer($userMessage, $userName, $dateTime='');
}



