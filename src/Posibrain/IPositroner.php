<?php

namespace Posibrain;


interface IPositroner {
	/**
	 * Load this bot's brain
	 * Return the Knowledge array
	 * Or NULL if no brain is retrieved
	 */
	public function loadPositrons();
	public function getPostitrons();
	public function findPositron($id);
	public function updatePositron($id, $state);
	
	public function callPre($functionName, $request=array());
	public function callPost($functionName, $request=array(), $currentResponse=array());
}