<?php
/**
 * This file handles anything related to core emoji functionality.
 *
 * @package TenUpPlugin/Core
 */

namespace TenUpPlugin\Core;

use TenupFramework\Module;
use TenupFramework\ModuleInterface;

/**
 * Emoji
 *
 * @package TenUpPlugin\Core
 */
class Emoji implements ModuleInterface {

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
		// Remove the Emoji detection script.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

		// Remove inline Emoji detection script.
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

		// Remove Emoji-related styles from front end and back end.
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );

		// Remove Emoji-to-static-img conversion.
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		add_filter( 'tiny_mce_plugins', [ $this, 'disable_emojis_tinymce' ] );
		add_filter( 'wp_resource_hints', [ $this, 'disable_emoji_dns_prefetch' ], 10, 2 );
	}

	/**
	 * Filter function used to remove the TinyMCE emoji plugin.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/tiny_mce_plugins/
	 *
	 * @param  array $plugins An array of default TinyMCE plugins.
	 * @return array          An array of TinyMCE plugins, without wpemoji.
	 */
	public function disable_emojis_tinymce( array $plugins ): array {
		if ( in_array( 'wpemoji', $plugins, true ) ) {
			return array_diff( $plugins, [ 'wpemoji' ] );
		}

		return $plugins;
	}

	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/emoji_svg_url/
	 *
	 * @param  array  $urls          URLs to print for resource hints.
	 * @param  string $relation_type The relation type the URLs are printed for.
	 * @return array                 Difference between the two arrays.
	 */
	public function disable_emoji_dns_prefetch( array $urls, string $relation_type ): array {
		if ( 'dns-prefetch' === $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_values( array_diff( $urls, [ $emoji_svg_url ] ) );
		}

		return $urls;
	}
}
