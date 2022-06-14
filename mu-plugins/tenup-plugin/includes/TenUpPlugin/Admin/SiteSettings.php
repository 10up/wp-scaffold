<?php
/**
 * SiteSettings
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\Admin;

/**
 * SiteSettings provides Fieldmanager based UI for global site settings.
 */
class SiteSettings extends \TenUpPlugin\Module {

	/**
	 * Fieldmanager Setting ID
	 *
	 * @var string
	 */
	public $name = 'tenup_plugin_settings';

	/**
	 * Creates a new Wildcard Route Settings Screen.
	 */
	public function register() {
		fm_register_submenu_page(
			$this->name,
			'options-general.php',
			__( 'Site Settings', 'tenup_plugin' )
		);

		add_action(
			'admin_bar_menu',
			[ $this, 'update_admin_bar_menu' ],
			1000
		);

		add_action(
			'fm_submenu_' . $this->name,
			[ $this, 'load' ]
		);

		add_filter(
			'fm_context_after_presave_data',
			[ $this, 'did_save' ],
			10,
			4
		);

		add_filter(
			'fm_context_before_presave_data',
			[ $this, 'will_save' ],
			10,
			4
		);
	}

	/**
	 * Only register if on admin page if fieldmanager plugin is active.
	 *
	 * @return bool
	 */
	public function can_register() {
		return is_admin() && function_exists( 'fm_register_submenu_page' );
	}

	/**
	 * Configures the site settings.
	 */
	public function load() {
		$config = [
			'name'     => $this->name,
			'children' => [
				'field_name' => new \Fieldmanager_TextField(
					[
						'label'       => 'Field Name',
						'description' => 'Field Description',
					]
				),
			],
		];

		/**
		 * Allow the filtering of the fields assigned to the site settings group.
		 *
		 * @param array $config The Fieldmanager config.
		 */
		$config = apply_filters( 'tenup_plugin_site_settings_config', $config );

		$fm = new \Fieldmanager_Group( $config );
		$fm->activate_submenu_page();
	}

	/**
	 * Flushes the object cache after settings are saved.
	 *
	 * @param array                $data The new data
	 * @param array                $old_value The old value
	 * @param Fieldmanager_Context $context The Fieldmanager page context
	 * @param Fieldmanager         $fm The fieldmanager instance
	 * @return array
	 */
	public function did_save( $data, $old_value, $context, $fm ) {
		if ( $fm->name === $this->name ) {
			wp_cache_flush();
		}

		return $data;
	}

	/**
	 * Runs any actions before the settings are saved.
	 *
	 * @param array                $data The new data
	 * @param array                $old_value The old value
	 * @param Fieldmanager_Context $context The Fieldmanager page context
	 * @param Fieldmanager         $fm The fieldmanager instance
	 * @return array
	 */
	public function will_save( $data, $old_value, $context, $fm ) {
		if ( $fm->name === $this->name ) {
			// no-op -- override this
		}

		return $data;
	}

	/**
	 * Adds a Site Settings menu item to the Admin Menu if logged in.
	 */
	public function update_admin_bar_menu() {
		global $wp_admin_bar;

		$menu = [
			'id'     => 'site_settings',
			'title'  => __( 'Site Settings', 'tenup_plugin' ),
			'href'   => home_url( '/wp-admin/options-general.php?page=tenup_plugin_settings' ),
			'parent' => 'site-name',
			'meta'   => [
				'target' => '_self',
				'title'  => 'Go to site settings page',
			],
		];

		$wp_admin_bar->add_menu( $menu );
	}

}
