<?php
namespace Posibrain\Positron;

use Posibrain\TchatMessage;
use Posibrain\AnalysedRequest;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
abstract class Positron
{

	public function isPositronTriggered(TchatMessage $request)
	{
		return true;
	}

	public function isBotTriggered(TchatMessage $request, $currentValue = true)
	{
		return $currentValue;
	}

	public function analyseRequest(TchatMessage $request, AnalysedRequest $currentAnalysedRequest = null)
	{
		if (null == $currentAnalysedRequest) {
			return new AnalysedRequest($request);
		}
		return $currentAnalysedRequest;
	}

	public function isPositronStillTriggered(AnalysedRequest $request)
	{
		return true;
	}

	public function isBotStillTriggered(AnalysedRequest $request, $currentValue = true)
	{
		return $currentValue;
	}

	public function loadMemory(AnalysedRequest $request, $currentMemory = null)
	{
		return $currentMemory;
	}

	public function transformLoadedMemory(AnalysedRequest $request, $memory, $currentMemory = null)
	{
		if (null == $currentMemory) {
			return $memory;
		}
		return $currentMemory;
	}

	public function generateSymbolicAnswer(AnalysedRequest $request, $memory, TchatMessage $currentAnswer = null)
	{
		return $currentAnswer;
	}

	public function provideMeaning(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
	{
		if (null == $currentAnswer) {
			return $answer;
		}
		return $currentAnswer;
	}

	public function beautifyAnswer(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null)
	{
		if (null == $currentAnswer) {
			return $answer;
		}
		return $currentAnswer;
	}

	public function updateMemory(AnalysedRequest $request, $memory, TchatMessage $answer)
	{}
}



