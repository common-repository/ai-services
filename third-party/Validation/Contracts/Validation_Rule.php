<?php

/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
/**
 * Interface for a validation rule.
 *
 * @since n.e.x.t
 */
interface Validation_Rule
{
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
    public function validate($value) : void;
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
    public function sanitize($value);
}
