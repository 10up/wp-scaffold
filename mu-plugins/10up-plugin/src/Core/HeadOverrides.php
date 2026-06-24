<?php
/**
 * This file contains hooks and functions that override the behavior of WP Core.
 *
 * @package TenUpPlugin/Core
 */

declare( strict_types = 1 );

namespace TenUpPlugin\Core;

use TenupFramework\Module;
use TenupFramework\ModuleInterface;

/**
 * Overrides class to manage WordPress core behavior modifications.
 *
 * @package TenUpPlugin\Core
 */
class HeadOverrides implements ModuleInterface {

	use Module;

	/**
	 * Used to alter the order in which classes are initialized.
	 *
	 * @var int The priority of the module.
	 */
	public $load_order = 5;

	/**
	 * Checks whether the Module should run within the current context.
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Connects the Module with WordPress using Hooks and/or Filters.
	 *
	 * @return void
	 */
	public function register(): void {
		// Remove WordPress generator meta.
		remove_action( 'wp_head', 'wp_generator' );
		// Remove Windows Live Writer manifest link.
		remove_action( 'wp_head', 'wlwmanifest_link' );
		// Remove the link to Really Simple Discovery service endpoint.
		remove_action( 'wp_head', 'rsd_link' );
	}
}
