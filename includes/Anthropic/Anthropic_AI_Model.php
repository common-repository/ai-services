<?php
/**
 * Class Felix_Arntz\AI_Services\Anthropic\Anthropic_AI_Model
 *
 * @since 0.1.0
 * @package ai-services
 */

namespace Felix_Arntz\AI_Services\Anthropic;

use Felix_Arntz\AI_Services\Services\Contracts\Generative_AI_Model;
use Felix_Arntz\AI_Services\Services\Contracts\With_Multimodal_Input;
use Felix_Arntz\AI_Services\Services\Contracts\With_Text_Generation;
use Felix_Arntz\AI_Services\Services\Exception\Generative_AI_Exception;
use Felix_Arntz\AI_Services\Services\Traits\With_Text_Generation_Trait;
use Felix_Arntz\AI_Services\Services\Types\Candidate;
use Felix_Arntz\AI_Services\Services\Types\Candidates;
use Felix_Arntz\AI_Services\Services\Types\Content;
use Felix_Arntz\AI_Services\Services\Types\Parts\Inline_Data_Part;
use Felix_Arntz\AI_Services\Services\Types\Parts\Text_Part;
use Felix_Arntz\AI_Services\Services\Util\Formatter;
use InvalidArgumentException;

/**
 * Class representing an Anthropic AI model.
 *
 * @since 0.1.0
 */
class Anthropic_AI_Model implements Generative_AI_Model, With_Multimodal_Input, With_Text_Generation {
	use With_Text_Generation_Trait;

	/**
	 * The Anthropic AI API instance.
	 *
	 * @since 0.1.0
	 * @var Anthropic_AI_API_Client
	 */
	private $api;

	/**
	 * The model slug.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $model;

	/**
	 * The generation configuration.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $generation_config;

	/**
	 * The system instruction.
	 *
	 * @since 0.1.0
	 * @var Content|null
	 */
	private $system_instruction;

	/**
	 * The request options.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $request_options;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Anthropic_AI_API_Client $api             The Anthropic AI API instance.
	 * @param string                  $model           The model slug.
	 * @param array<string, mixed>    $model_params    Optional. Additional model parameters. See
	 *                                                 {@see Anthropic_AI_Service::get_model()} for the list of
	 *                                                 available parameters. Default empty array.
	 * @param array<string, mixed>    $request_options Optional. The request options. Default empty array.
	 *
	 * @throws InvalidArgumentException Thrown if the model parameters are invalid.
	 */
	public function __construct( Anthropic_AI_API_Client $api, string $model, array $model_params = array(), array $request_options = array() ) {
		$this->api             = $api;
		$this->request_options = $request_options;

		$this->model = $model;

		$this->generation_config = $model_params['generation_config'] ?? array();

		if ( isset( $model_params['system_instruction'] ) ) {
			$this->system_instruction = Formatter::format_system_instruction( $model_params['system_instruction'] );
		}
	}

	/**
	 * Gets the model slug.
	 *
	 * @since 0.1.0
	 *
	 * @return string The model slug.
	 */
	public function get_model_slug(): string {
		return $this->model;
	}

	/**
	 * Sends a request to generate text content.
	 *
	 * @since 0.1.0
	 *
	 * @param Content[]            $contents        Prompts for the content to generate.
	 * @param array<string, mixed> $request_options The request options.
	 * @return Candidates The response candidates with generated text content - usually just one.
	 *
	 * @throws Generative_AI_Exception Thrown if the request fails or the response is invalid.
	 */
	protected function send_generate_text_request( array $contents, array $request_options ): Candidates {
		$params = array(
			// TODO: Add support for tools and tool config, to support code generation.
			'messages'   => array_map(
				array( $this, 'prepare_content_for_api_request' ),
				$contents
			),
			'max_tokens' => $this->generation_config['maxOutputTokens'] ?? 4096,
		);
		if ( isset( $this->generation_config['temperature'] ) ) {
			$params['temperature'] = $this->generation_config['temperature'];
		}
		if ( isset( $this->generation_config['stopSequences'] ) ) {
			$params['stop_sequences'] = $this->generation_config['stopSequences'];
		}
		if ( $this->system_instruction ) {
			$params['system'] = $this->system_instruction
				->get_parts()
				->filter( array( 'class_name' => Text_Part::class ) )
				->get( 0 )
				->to_array()['text'];
		}

		$request  = $this->api->create_generate_content_request(
			$this->model,
			array_filter( $params ),
			array_merge(
				$this->request_options,
				$request_options
			)
		);
		$response = $this->api->make_request( $request );

		$candidates = new Candidates();
		$candidates->add_candidate(
			new Candidate(
				$this->prepare_api_response_for_content( $response ),
				$response
			)
		);

		return $candidates;
	}

	/**
	 * Transforms a given Content instance into the format required for the API request.
	 *
	 * @since 0.1.0
	 *
	 * @param Content $content The content instance.
	 * @return array<string, mixed> The content data for the API request.
	 *
	 * @throws InvalidArgumentException Thrown if the content is invalid.
	 */
	private function prepare_content_for_api_request( Content $content ): array {
		if ( $content->get_role() === Content::ROLE_MODEL ) {
			$role = 'assistant';
		} else {
			$role = 'user';
		}

		$parts = array();
		foreach ( $content->get_parts() as $part ) {
			if ( $part instanceof Text_Part ) {
				$data    = $part->to_array();
				$parts[] = array(
					'type' => 'text',
					'text' => $data['text'],
				);
			} elseif ( $part instanceof Inline_Data_Part ) {
				$data = $part->to_array();
				if ( ! str_starts_with( $data['inlineData']['mimeType'], 'image/' ) ) {
					throw new InvalidArgumentException(
						esc_html__( 'Invalid content part: The Anthropic API only supports text and inline image parts.', 'ai-services' )
					);
				}
				$parts[] = array(
					'type'   => 'image',
					'source' => array(
						'type'       => 'base64',
						'media_type' => $data['inlineData']['mimeType'],
						'data'       => $data['inlineData']['data'],
					),
				);
			} else {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid content part: The Anthropic API only supports text and inline image parts.', 'ai-services' )
				);
			}
		}

		return array(
			'role'    => $role,
			'content' => $parts,
		);
	}

	/**
	 * Transforms a given API response into a Content instance.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $response The API response.
	 * @return Content The Content instance.
	 *
	 * @throws Generative_AI_Exception Thrown if the response is invalid.
	 */
	private function prepare_api_response_for_content( array $response ): Content {
		if ( ! isset( $response['content'] ) || ! $response['content'] ) {
			throw new Generative_AI_Exception(
				esc_html(
					sprintf(
						/* translators: %s: key name */
						__( 'The response from the Anthropic API is missing the "%s" key.', 'ai-services' ),
						'content'
					)
				)
			);
		}

		$role = isset( $response['role'] ) && 'user' === $response['role']
			? Content::ROLE_USER
			: Content::ROLE_MODEL;

		$parts = array();
		foreach ( $response['content'] as $part ) {
			// TODO: Support decoding tool call responses.
			if ( 'text' === $part['type'] ) {
				$parts[] = array( 'text' => $part['text'] );
			} else {
				throw new Generative_AI_Exception(
					esc_html__( 'The response from the Anthropic API includes an unexpected content part.', 'ai-services' )
				);
			}
		}

		return Content::from_array(
			array(
				'role'  => $role,
				'parts' => $parts,
			)
		);
	}
}
