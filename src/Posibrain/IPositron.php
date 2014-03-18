<?php

namespace Posibrain;

/**
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 */
interface IPositron
{
	public function preGenerateAnswer($request=array());

	public function preIsTriggered($request=array());

	public function postIsTriggered($request=array(), $currentResponse=array());

	public function postGenerateAnswer($request=array(), $currentResponse=array());
}



