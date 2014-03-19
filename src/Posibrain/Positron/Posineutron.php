<?php
namespace Posibrain\Positron;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class Posineutron implements IPositron
{

	public function preIsTriggered($request = array())
	{
		return $request;
	}

	public function postIsTriggered($request = array(), $currentAnswer = array())
	{
		return true;
	}

	public function preGenerateAnswer($request = array())
	{
		return $request;
	}

	public function postGenerateAnswer($request = array(), $currentAnswer = array())
	{
		return $currentAnswer;
	}
}
