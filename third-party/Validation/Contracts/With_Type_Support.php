<?php

/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

/**
 * Interface for a validation rule that supports specific types.
 *
 * Any validation rule that does not support _any_ input value must implement this interface.
 *
 * @since n.e.x.t
 */
interface With_Type_Support
{
    /**
     * Checks whether the validation rule supports values of the given type.
     *
     * This method is mostly for internal use, e.g. to ensure that builders don't allow rules that are useless for them.
     *
     * @since n.e.x.t
     *
     * @param int $type One of the type constants from the Types interface.
     * @return bool True if the given type is supported, false otherwise.
     */
    public function supports_type(int $type) : bool;
}
