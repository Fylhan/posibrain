<?php
namespace Posibrain;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
interface IPositroner
{

	/**
	 * Load this bot's brain
	 * Return the Knowledge array
	 * Or NULL if no brain is retrieved
	 */
	public function loadPositrons($config, $params=array());

	public function getPostitrons();

	public function findPositron($id);

	public function updatePositron($id, $state);

	public function isTriggered(TchatMessage $request, $currentValue = true);

	public function analyseRequest(TchatMessage $request, AnalysedRequest $currentAnalysedRequest = null);

	public function loadMemory(AnalysedRequest $request, $currentMemory = null);

	public function generateSymbolicAnswer(AnalysedRequest $request, $memory, TchatMessage $currentAnswer = null);

	public function provideMeaning(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null);
	
	public function beautifyAnswer(AnalysedRequest $request, $memory, TchatMessage $answer, TchatMessage $currentAnswer = null);
}