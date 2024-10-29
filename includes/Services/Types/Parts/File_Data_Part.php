<?php
/**
 * Class Felix_Arntz\AI_Services\Services\Types\Parts\File_Data_Part
 *
 * @since 0.1.0
 * @package ai-services
 */

namespace Felix_Arntz\AI_Services\Services\Types\Parts;

use InvalidArgumentException;

/**
 * Class for a file data part of content for a generative model.
 *
 * @since 0.1.0
 */
final class File_Data_Part extends Abstract_Part {

	/**
	 * Formats the data for the part.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $data The part data.
	 * @return array<string, mixed> Formatted data.
	 *
	 * @throws InvalidArgumentException Thrown if the part data is invalid.
	 */
	protected function format_data( array $data ): array {
		if ( ! isset( $data['fileData'] ) || ! is_array( $data['fileData'] ) ) {
			throw new InvalidArgumentException( 'The file data part data must contain an associative array fileData value.' );
		}

		$file_data = $data['fileData'];

		if ( ! isset( $file_data['mimeType'] ) || ! is_string( $file_data['mimeType'] ) ) {
			throw new InvalidArgumentException( 'The file data part data must contain a string mimeType value.' );
		}

		if ( ! isset( $file_data['fileUri'] ) || ! is_string( $file_data['fileUri'] ) ) {
			throw new InvalidArgumentException( 'The file data part data must contain a string fileUri value.' );
		}

		return array(
			'fileData' => array(
				'mimeType' => $file_data['mimeType'],
				'fileUri'  => $file_data['fileUri'],
			),
		);
	}

	/**
	 * Gets the default data for the part.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Default data.
	 */
	protected function get_default_data(): array {
		return array(
			'fileData' => array(
				'mimeType' => '',
				'fileUri'  => '',
			),
		);
	}
}
