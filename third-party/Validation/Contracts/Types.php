<?php

/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

/**
 * Interface acting as an enum for validation types.
 *
 * @since n.e.x.t
 */
interface Types
{
    const TYPE_BOOLEAN = 1;
    const TYPE_FLOAT = 2;
    const TYPE_INTEGER = 4;
    const TYPE_STRING = 8;
    const TYPE_ARRAY = 16;
    const TYPE_OBJECT = 32;
    // This is the same as all of the above with a bitwise OR operator (|).
    const TYPE_ANY = 63;
}
