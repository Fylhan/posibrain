<?php
namespace Posibrain\Positron\Instinct;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
interface IBrainManager
{

	/**
	 * Load this bot's brain
	 * Return the Knowledge array
	 * Or NULL if no brain is retrieved
	 */
	public function loadBrain($config);
}