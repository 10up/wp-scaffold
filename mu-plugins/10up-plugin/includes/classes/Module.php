<?php
/**
 * Module
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin;

/**
 * Module is any feature that conditionally activates based on the current context.
 */
abstract class Module {

	/**
	 * Checks whether the Module should run within the current context.
	 *
	 * @return bool
	 */
	abstract public function can_register();

	/**
	 * Connects the Module with WordPress using Hooks and/or Filters.
	 *
	 * @return void
	 */
	abstract public function register();

}
