<?php
/**
 * Interface Felix_Arntz\AI_Services\Services\Contracts\With_API_Client
 *
 * @since 0.1.0
 * @package ai-services
 */

namespace Felix_Arntz\AI_Services\Services\Contracts;

/**
 * Interface for a class which contains a client for a generative AI web API.
 *
 * @since 0.1.0
 */
interface With_API_Client {

	/**
	 * Gets the API client instance.
	 *
	 * @since 0.1.0
	 *
	 * @return Generative_AI_API_Client The API client instance.
	 */
	public function get_api_client(): Generative_AI_API_Client;
}
