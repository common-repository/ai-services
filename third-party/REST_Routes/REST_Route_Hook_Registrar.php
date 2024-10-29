<?php

/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\REST_Route_Hook_Registrar
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes;

use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Hook_Registrar;
/**
 * Class that adds the relevant hook to register WordPress REST routes.
 *
 * @since n.e.x.t
 */
class REST_Route_Hook_Registrar implements Hook_Registrar
{
    /**
     * WordPress REST route registry instance.
     *
     * @since n.e.x.t
     * @var REST_Route_Registry
     */
    private $registry;
    /**
     * Constructor.
     *
     * @param REST_Route_Registry $registry WordPress REST route registry instance.
     */
    public function __construct(REST_Route_Registry $registry)
    {
        $this->registry = $registry;
    }
    /**
     * Adds a callback that registers the REST routes to the relevant hook.
     *
     * The callback receives a registry instance as the sole parameter, allowing to call the
     * {@see REST_Route_Registry::register()} method.
     *
     * @since n.e.x.t
     *
     * @param callable $register_callback Callback to register the REST routes.
     */
    public function add_register_callback(callable $register_callback) : void
    {
        add_action('rest_api_init', function () use($register_callback) {
            $register_callback($this->registry);
        });
    }
}
