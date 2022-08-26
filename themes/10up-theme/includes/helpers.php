<?php
/**
 * Theme specific helpers.
 *
 * @package TenUpTheme
 */

namespace TenUpTheme;

/**
 * Get an initialized class by its full class name, including namespace.
 *
 * @param string $class_name The class name including the namespace.
 *
 * @return false|Module
 */
function get_module( $class_name ) {
	return \TenUpTheme\ModuleInitialization::instance()->get_class( $class_name );
}
