<?php
/**
 * Class Felix_Arntz\AI_Services\Services\Services_API
 *
 * @since 0.1.0
 * @package ai-services
 */

namespace Felix_Arntz\AI_Services\Services;

use Felix_Arntz\AI_Services\Services\Cache\Service_Request_Cache;
use Felix_Arntz\AI_Services\Services\Contracts\Generative_AI_Service;
use Felix_Arntz\AI_Services\Services\Contracts\With_API_Client;
use Felix_Arntz\AI_Services\Services\Exception\Generative_AI_Exception;
use Felix_Arntz\AI_Services\Services\Options\Option_Encrypter;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\General\Current_User;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\HTTP;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Container;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Repository;
use InvalidArgumentException;

/**
 * Main API class providing the entry point to the generative AI services.
 *
 * @since 0.1.0
 */
final class Services_API {

	/**
	 * The service registration definitions, keyed by service slug.
	 *
	 * @since 0.1.0
	 * @var array<string, Service_Registration>
	 */
	private $service_registrations = array();

	/**
	 * The service instances, keyed by service slug.
	 *
	 * @since 0.1.0
	 * @var array<string, Generative_AI_Service>
	 */
	private $service_instances = array();

	/**
	 * The current user instance.
	 *
	 * @since 0.1.0
	 * @var Current_User
	 */
	private $current_user;

	/**
	 * The option container instance.
	 *
	 * @since 0.1.0
	 * @var Option_Container
	 */
	private $option_container;

	/**
	 * The option repository instance.
	 *
	 * @since 0.1.0
	 * @var Option_Repository
	 */
	private $option_repository;

	/**
	 * The option encrypter instance.
	 *
	 * @since 0.1.0
	 * @var Option_Encrypter
	 */
	private $option_encrypter;

	/**
	 * The HTTP instance.
	 *
	 * @since 0.1.0
	 * @var HTTP
	 */
	private $http;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Current_User      $current_user      The current user instance.
	 * @param Option_Container  $option_container  The option container instance.
	 * @param Option_Repository $option_repository The option repository instance.
	 * @param Option_Encrypter  $option_encrypter  The option encrypter instance.
	 * @param HTTP              $http              The HTTP instance.
	 */
	public function __construct(
		Current_User $current_user,
		Option_Container $option_container,
		Option_Repository $option_repository,
		Option_Encrypter $option_encrypter,
		HTTP $http
	) {
		$this->current_user      = $current_user;
		$this->option_container  = $option_container;
		$this->option_repository = $option_repository;
		$this->option_encrypter  = $option_encrypter;
		$this->http              = $http;
	}

	/**
	 * Registers a generative AI service.
	 *
	 * An AI service consists at least of a service class that implements the Generative_AI_Service interface and a
	 * model class that implements the Generative_AI_Model interface. Optionally, the service class can implement the
	 * With_API_Client interface, if the service uses an API client class. Doing so benefits performance, as it allows
	 * the infrastructure to perform batch requests across multiple services.
	 *
	 * Consumers of the service will access the service class through a proxy wrapper class which automatically handles
	 * caching and other infrastructure concerns. It is therefore advised to not implement any caching concerns in the
	 * service class itself as well as to not implement any public methods other than those required by the relevant
	 * interfaces.
	 *
	 * The $creator parameter of this method needs to return the instance of the service class.
	 *
	 * @since 0.1.0
	 *
	 * @see Generative_AI_Service
	 * @see With_API_Client
	 *
	 * @param string               $slug    The service slug. Must only contain lowercase letters, numbers, hyphens. It
	 *                                      must be unique and must match the service slug returned by the service
	 *                                      class.
	 * @param callable             $creator The service creator. Receives the Authentication instance as first
	 *                                      parameter, the HTTP instance as second parameter, and must return a
	 *                                      Generative_AI_Service instance.
	 * @param array<string, mixed> $args    {
	 *     Optional. The service arguments. Default empty array.
	 *
	 *     @type string $name            The user-facing service name. Default is the slug with spaces and uppercase
	 *                                   first letters.
	 *     @type string $credentials_url The URL to manage credentials for the service. Default empty string.
	 *     @type bool   $allow_override  Whether the service can be overridden by another service with the same slug.
	 *                                   Default true.
	 * }
	 *
	 * @throws InvalidArgumentException Thrown if an already registered slug or invalid arguments are provided.
	 */
	public function register_service( string $slug, callable $creator, array $args = array() ): void {
		if ( 'browser' === $slug ) {
			throw new InvalidArgumentException(
				esc_html(
					sprintf(
						/* translators: %s: The service slug. */
						esc_html__( 'Service %s is reserved for in-browser AI and cannot be registered.', 'ai-services' ),
						$slug
					)
				)
			);
		}

		if ( isset( $this->service_registrations[ $slug ] ) && ! $this->service_registrations[ $slug ]->allows_override() ) {
			throw new InvalidArgumentException(
				esc_html(
					sprintf(
						/* translators: %s: The service slug. */
						esc_html__( 'Service %s is already registered and cannot be overridden.', 'ai-services' ),
						$slug
					)
				)
			);
		}

		$args['option_container']  = $this->option_container;
		$args['option_repository'] = $this->option_repository;
		$args['http']              = $this->http;

		$this->service_registrations[ $slug ] = new Service_Registration( $slug, $creator, $args );

		$option_slugs = $this->service_registrations[ $slug ]->get_authentication_option_slugs();
		foreach ( $option_slugs as $option_slug ) {
			// Ensure the authentication options are encrypted.
			if ( ! $this->option_encrypter->has_encryption( $option_slug ) ) {
				$this->option_encrypter->add_encryption_hooks( $option_slug );
			}

			// Ensure any service request caches are invalidated when the authentication credentials change.
			$invalid_service_caches = static function () use ( $slug ) {
				Service_Request_Cache::invalidate_caches( $slug );
			};
			add_action( "add_option_{$option_slug}", $invalid_service_caches );
			add_action( "update_option_{$option_slug}", $invalid_service_caches );
			add_action( "delete_option_{$option_slug}", $invalid_service_caches );
		}
	}

	/**
	 * Checks whether a service is registered.
	 *
	 * @since 0.1.0
	 *
	 * @param string $slug The service slug.
	 * @return bool True if the service is registered, false otherwise.
	 */
	public function is_service_registered( string $slug ): bool {
		return isset( $this->service_registrations[ $slug ] );
	}

	/**
	 * Checks whether a service is available.
	 *
	 * For a service to be considered available, all of the following conditions must be met:
	 * - The service is registered.
	 * - The service has an API key set.
	 * - The API key is valid.
	 * - The current user has the necessary capabilities.
	 *
	 * @since 0.1.0
	 *
	 * @param string $slug The service slug.
	 * @return bool True if the service is available, false otherwise.
	 */
	public function is_service_available( string $slug ): bool {
		/*
		 * If the service was already instantiated in the class, it is available.
		 * In that case, the only thing left to check is whether the current user has the necessary capabilities.
		 */
		if ( isset( $this->service_instances[ $slug ] ) ) {
			if ( ! $this->current_user->has_cap( 'ais_access_service', $slug ) ) {
				return false;
			}
			return true;
		}

		// If the service is not registered, it is not available.
		if ( ! isset( $this->service_registrations[ $slug ] ) ) {
			return false;
		}

		// If any authentication credentials are missing for the service, it is not available.
		$authentication_options = $this->service_registrations[ $slug ]->get_authentication_options();
		foreach ( $authentication_options as $option ) {
			if ( ! $option->get_value() ) {
				return false;
			}
		}

		// Test whether the API key is valid by listing the models.
		$instance = $this->service_registrations[ $slug ]->create_instance();
		try {
			$instance->list_models();
		} catch ( Generative_AI_Exception $e ) {
			return false;
		}

		// If so, the service is available so we can store the instance.
		$this->service_instances[ $slug ] = $instance;

		// Finally, check whether the current user has the necessary capabilities.
		return $this->current_user->has_cap( 'ais_access_service', $slug );
	}

	/**
	 * Checks whether any services are available.
	 *
	 * For some use-cases it may be acceptable to use any AI service. In those cases, this method can be used to check
	 * whether any services are available. If so, an arbitrary available service can be retrieved using the
	 * {@see Services_API::get_available_service()} method.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $args {
	 *     Optional. Arguments to filter the services to consider. By default, any available service is considered.
	 *
	 *     @type string[] $slugs        List of service slugs, to only consider any of these services.
	 *     @type string[] $capabilities List of AI capabilities, to only consider services that support all of these
	 *                                  capabilities.
	 * }
	 * @return bool True if any of the services are available, false otherwise.
	 */
	public function has_available_services( array $args = array() ): bool {
		$slug = $this->get_available_service_slug( $args );
		return '' !== $slug;
	}

	/**
	 * Gets a generative AI service instance that is available for use.
	 *
	 * If you intend to call this method with a specific service slug, you should first check whether the service is
	 * available using {@see Services_API::is_service_available()}.
	 *
	 * If you intend to call this method to get any service (optionally with additional criteria to satisfy), you
	 * should first check if any of the services are available using {@see Services_API::has_available_services()}.
	 *
	 * @since 0.1.0
	 *
	 * @param string|array<string, mixed> $args Optional. Either a single service slug to get that service, or
	 *                                          arguments to get any service that satisfies the criteria from these
	 *                                          arguments. See {@see Services_API::has_available_services()} for the
	 *                                          possible arguments. Default is an empty array so that any available
	 *                                          service is considered.
	 * @return Generative_AI_Service The available service instance.
	 *
	 * @throws InvalidArgumentException Thrown if no service corresponding to the given arguments is available.
	 */
	public function get_available_service( $args = array() ): Generative_AI_Service {
		if ( is_string( $args ) ) {
			$slug = $args;
			if ( ! $this->is_service_available( $slug ) ) {
				throw new InvalidArgumentException(
					esc_html(
						sprintf(
							/* translators: %s: The service slug. */
							__( 'Service %s is either not registered or not available.', 'ai-services' ),
							$slug
						)
					)
				);
			}

			return $this->service_instances[ $slug ];
		}

		$slug = $this->get_available_service_slug( $args );
		if ( '' === $slug ) {
			if ( count( $args ) > 0 ) {
				$message = __( 'No service satisfying the given arguments is registered and available.', 'ai-services' );
			} else {
				$message = __( 'No service is registered and available.', 'ai-services' );
			}
			throw new InvalidArgumentException( esc_html( $message ) );
		}

		return $this->service_instances[ $slug ];
	}

	/**
	 * Gets the service name.
	 *
	 * @since 0.1.0
	 *
	 * @param string $slug The service slug.
	 * @return string The service name, or empty string if the service is not registered.
	 */
	public function get_service_name( string $slug ): string {
		if ( ! isset( $this->service_registrations[ $slug ] ) ) {
			return '';
		}

		return $this->service_registrations[ $slug ]->get_name();
	}

	/**
	 * Gets the service credentials URL.
	 *
	 * @since 0.1.0
	 *
	 * @param string $slug The service slug.
	 * @return string The service credentials URL, or empty string if the service is not registered or if no
	 *                credentials URL is specified.
	 */
	public function get_service_credentials_url( string $slug ): string {
		if ( ! isset( $this->service_registrations[ $slug ] ) ) {
			return '';
		}

		return $this->service_registrations[ $slug ]->get_credentials_url();
	}

	/**
	 * Gets the list of all registered service slugs.
	 *
	 * @since 0.1.0
	 *
	 * @return string[] The list of registered service slugs.
	 */
	public function get_registered_service_slugs(): array {
		return array_keys( $this->service_registrations );
	}

	/**
	 * Gets the first available service slug, optionally satisfying the given criteria.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $args Optional. Arguments to filter the services to consider. See
	 *                                   {@see Services_API::has_available_services()} for the possible arguments.
	 *                                   By default, any available service is considered.
	 * @return string The first available service slug, or empty string if no service is available.
	 */
	private function get_available_service_slug( array $args = array() ): string {
		$slugs = $args['slugs'] ?? $this->get_registered_service_slugs();

		foreach ( $slugs as $slug ) {
			if ( ! $this->is_service_available( $slug ) ) {
				continue;
			}

			if ( isset( $args['capabilities'] ) ) {
				$missing_capabilities = array_diff( $args['capabilities'], $this->service_instances[ $slug ]->get_capabilities() );
				if ( count( $missing_capabilities ) > 0 ) {
					continue;
				}
			}

			return $slug;
		}

		return '';
	}
}
