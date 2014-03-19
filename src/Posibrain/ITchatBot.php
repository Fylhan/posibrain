<?php
namespace Posibrain;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
interface ITchatBot
{

	/**
	 * To know if the bot should be triggered
	 *
	 * @param string $userMessage
	 *        	Message of the user
	 * @param string $userName
	 *        	Name of the user who speak to the bot
	 * @param long|\DateTime|string $dateTime
	 *        	Date when the message had been posted. Unix timestamp or \DateTime or string representing \DateTime are accepted. Current date by default.
	 * @return boolean
	 */
	public function isTriggered($userMessage, $userName = '', $dateTime = 0);

	/**
	 * Generate to the user sentence
	 * 
	 * @param string $userMessage
	 *        	Message of the user
	 * @param string $userName
	 *        	Name of the user who speak to the bot
	 * @param long|\DateTime|string $dateTime
	 *        	Date when the message had been posted. Unix timestamp or \DateTime or string representing \DateTime are accepted. Current date by default.
	 * @return array($botName, $botMessage)
	 */
	public function generateAnswer($userMessage, $userName = '', $dateTime = 0);
}



