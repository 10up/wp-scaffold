<?php
/**
 * Utility functions for the plugin.
 *
 * This file is for custom helper functions.
 * These should not be confused with WordPress template
 * tags. Template tags typically use prefixing, as opposed
 * to Namespaces.
 *
 * @link https://developer.wordpress.org/themes/basics/template-tags/
 * @package TenUpPlugin
 */

namespace TenUpPlugin\Utility;

/**
 * Get asset info from extracted asset files
 *
 * @param string $slug Asset slug as defined in build/webpack configuration
 * @param string $attribute Optional attribute to get. Can be version or dependencies
 * @return ($attribute is null ? array{version: string, dependencies: array<string>} : $attribute is 'dependencies' ? array<string> : string)
 */
function get_asset_info( $slug, $attribute = null ) {
	if ( file_exists( TENUP_PLUGIN_PATH . 'dist/js/' . $slug . '.asset.php' ) ) {
		$asset = require TENUP_PLUGIN_PATH . 'dist/js/' . $slug . '.asset.php';
	} elseif ( file_exists( TENUP_PLUGIN_PATH . 'dist/css/' . $slug . '.asset.php' ) ) {
		$asset = require TENUP_PLUGIN_PATH . 'dist/css/' . $slug . '.asset.php';
	} else {
		$asset = [
			'version'      => TENUP_PLUGIN_VERSION,
			'dependencies' => [],
		];
	}

	// @var <array{version: string, dependencies: array<string>}> $asset

	if ( ! empty( $attribute ) && isset( $asset[ $attribute ] ) ) {
		return $asset[ $attribute ];
	}

	return $asset;
}
