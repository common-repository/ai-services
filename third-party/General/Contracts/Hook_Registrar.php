<?php

/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Hook_Registrar
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for a class that adds the relevant hook to register items.
 *
 * @since n.e.x.t
 */
interface Hook_Registrar
{
    /**
     * Adds a callback that registers the items to the relevant hook.
     *
     * The callback receives a registry instance as the sole parameter, allowing to call the
     * {@see Registry::register()} method.
     *
     * @since n.e.x.t
     *
     * @param callable $register_callback Callback to register the items.
     */
    public function add_register_callback(callable $register_callback) : void;
}
