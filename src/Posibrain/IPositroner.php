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
	public function loadPositrons($config);

	public function getPostitrons();

	public function findPositron($id);

	public function updatePositron($id, $state);

	public function callPre($functionName, $request = array());

	public function callPost($functionName, $request = array(), $currentResponse = array());
}