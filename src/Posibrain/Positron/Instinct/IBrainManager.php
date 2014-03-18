<?php

namespace Posibrain\Positron\Instinct;


interface IBrainManager {
	/**
	 * Load this bot's brain
	 * Return the Knowledge array
	 * Or NULL if no brain is retrieved
	 */
	public function loadBrain($config);
}