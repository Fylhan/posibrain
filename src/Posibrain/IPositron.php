<?php

namespace Posibrain;

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @created 2013-11-05
 * @updated 2013-11-05
 */
interface IPositron
{
	public function beforeGenerateAnswer($userName, $userMessage, $userMessageDateTime);

	public function beforeLoadBrain($userName, $userMessage, $userMessageDateTime);

	public function afterLoadBrain($userName, $userMessage, $userMessageDateTime, $brain);

	public function beforeIsTriggered($userName, $userMessage, $userMessageDateTime);

	public function afterIsTriggered($userName, $userMessage, $userMessageDateTime, $isTriggered);

	public function afterGenerateAnswer($userName, $userMessage, $userMessageDateTime, $botName, $botMessage, $botMessageDateTime);
}



