<?php
/**
 * TemplateTags module.
 *
 * @package TenupBlockTheme
 */

namespace TenupBlockTheme;

use TenupFramework\Module;
use TenupFramework\ModuleInterface;

/**
 * TemplateTags module.
 *
 * @package TenupBlockTheme
 */
class TemplateTags implements ModuleInterface {

	use Module;

	/**
	 * Can this module be registered?
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Register any hooks and filters.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_head', [ $this, 'add_viewport_meta_tag' ], 10, 0 );
	}

	/**
	 * Add viewport meta tag to head.
	 *
	 * @return void
	 */
	public function add_viewport_meta_tag(): void {
		?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
		<?php
	}
}
