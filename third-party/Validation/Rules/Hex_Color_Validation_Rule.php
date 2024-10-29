<?php

/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Hex_Color_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules;

use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Type_Support;
/**
 * Class for a validation rule that ensures values are valid hexadecimal color strings.
 *
 * @since n.e.x.t
 */
class Hex_Color_Validation_Rule implements Validation_Rule, With_Type_Support
{
    use Type_Support;
    /**
     * Validates the given value.
     *
     * Validation will be strict and throw an exception for any unmet requirements.
     *
     * @since n.e.x.t
     *
     * @param mixed $value Value to validate.
     *
     * @throws Validation_Exception Thrown when validation fails.
     */
    public function validate($value) : void
    {
        if (!rest_parse_hex_color((string) $value)) {
            throw Validation_Exception::create('invalid_hex_color', \sprintf(
                /* translators: %s: value */
                esc_html__('%s is not a valid hex color.', 'ai-services'),
                esc_html((string) $value)
            ));
        }
    }
    /**
     * Sanitizes the given value.
     *
     * This should be called before storing the value in the persistency layer (e.g. the database).
     * If the value does not satisfy validation requirements, it will be sanitized to a value that does, e.g. a default.
     *
     * @since n.e.x.t
     *
     * @param mixed $value Value to sanitize.
     * @return mixed Sanitized value.
     */
    public function sanitize($value)
    {
        return (string) sanitize_hex_color((string) $value);
    }
    /**
     * Gets the supported types for the validation rule.
     *
     * @since n.e.x.t
     *
     * @return int One or more of the type constants from the Types interface, combined with a bitwise OR.
     */
    protected function get_supported_types() : int
    {
        return Types::TYPE_STRING;
    }
}
