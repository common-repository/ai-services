<?php
/**
 * Class Felix_Arntz\AI_Services\Services\Services_Loader
 *
 * @since 0.1.0
 * @package ai-services
 */

namespace Felix_Arntz\AI_Services\Services;

use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Hooks;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\General\Service_Container;

/**
 * Loader class responsible for initializing the AI services functionality, including its public API.
 *
 * @since 0.1.0
 */
final class Services_Loader implements With_Hooks {

	/**
	 * Service container for the class's dependencies.
	 *
	 * @since 0.1.0
	 * @var Service_Container
	 */
	private $container;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 */
	public function __construct( string $main_file ) {
		$this->container = $this->set_up_container( $main_file );
		Services_API_Instance::set( $this->container['api'] );
	}

	/**
	 * Adds relevant WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function add_hooks(): void {
		$this->load_capabilities();
		$this->load_dependencies();
		$this->load_options();
		$this->load_rest_routes();
		$this->load_settings_page();
	}

	/**
	 * Loads the services capabilities and sets up the relevant filters.
	 *
	 * @since 0.1.0
	 */
	private function load_capabilities(): void {
		add_action(
			'plugins_loaded',
			function () {
				$controller = $this->container['capability_controller'];

				/**
				 * Fires when the services capabilities are loaded.
				 *
				 * This hook allows you to modify the rules for how these capabilities are granted. The capabilities
				 * available in the controller are:
				 * - 'ais_manage_services' (base capability)
				 * - 'ais_access_services' (base capability)
				 * - 'ais_access_service' (meta capability, called with the specific service slug as parameter)
				 *
				 * @since 0.1.0
				 *
				 * @param Capability_Controller $controller The capability controller, which can be used to modify the
				 *                                          rules for how capabilities are granted.
				 */
				do_action( 'ais_load_services_capabilities', $controller );

				$this->container['capability_filters']->add_hooks();
			},
			0
		);
	}

	/**
	 * Registers the JS & CSS dependencies for the AI services.
	 *
	 * @since 0.1.0
	 */
	private function load_dependencies(): void {
		add_action(
			'init',
			function () {
				$this->container['services_script_style_loader']->register_scripts_and_styles();
			}
		);
	}

	/**
	 * Loads the services options.
	 *
	 * The option container is populated with options dynamically based on registered AI services. Each of the relevant
	 * options will be registered here.
	 *
	 * @since 0.1.0
	 */
	private function load_options(): void {
		add_action(
			'init',
			function () {
				$registry = $this->container['option_registry'];
				foreach ( $this->container['option_container']->get_keys() as $key ) {
					$option = $this->container['option_container']->get( $key );
					$registry->register(
						$option->get_key(),
						$option->get_registration_args()
					);
				}
			},
			0
		);
	}

	/**
	 * Loads the plugin's REST API routes..
	 *
	 * @since 0.1.0
	 */
	private function load_rest_routes(): void {
		add_action(
			'rest_api_init',
			function () {
				foreach ( $this->container['rest_route_collection'] as $rest_route ) {
					$this->container['rest_route_registry']->register(
						$rest_route->get_base(),
						$rest_route->get_registration_args()
					);
				}
			}
		);
	}

	/**
	 * Loads the services settings page.
	 *
	 * @since 0.1.0
	 */
	private function load_settings_page(): void {
		add_action(
			'admin_menu',
			function () {
				$this->container['admin_settings_menu']->add_page( $this->container['admin_settings_page'] );
			}
		);
	}

	/**
	 * Sets up the services service container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 * @return Service_Container The services service container.
	 */
	private function set_up_container( string $main_file ): Service_Container {
		$builder = new Services_Service_Container_Builder();

		return $builder->build_env( $main_file )
			->build_services()
			->get();
	}
}
