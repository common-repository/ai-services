<?php

/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Options;

use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Key_Value_Repository;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Options\Contracts\With_Autoload_Config;
/**
 * Class for a repository of WordPress options.
 *
 * @since n.e.x.t
 */
class Option_Repository implements Key_Value_Repository, With_Autoload_Config
{
    /**
     * Autoload config as $key => $autoload pairs.
     *
     * @since n.e.x.t
     * @var array<string, bool>
     */
    private $autoload_config = array();
    /**
     * Checks whether a value for the given option exists in the database.
     *
     * @since n.e.x.t
     *
     * @param string $key Option key.
     * @return bool True if a value for the option exists, false otherwise.
     */
    public function exists(string $key) : bool
    {
        $value = get_option($key, null);
        return null !== $value;
    }
    /**
     * Gets the value for a given option from the database.
     *
     * @since n.e.x.t
     *
     * @param string $key     Option key.
     * @param mixed  $default Optional. Value to return if no value exists for the option. Default null.
     * @return mixed Value for the option, or the default if no value exists.
     */
    public function get(string $key, $default = null)
    {
        return get_option($key, $default);
    }
    /**
     * Updates the value for a given option in the database.
     *
     * @since n.e.x.t
     *
     * @param string $key   Option key.
     * @param mixed  $value New value to set for the option.
     * @return bool True on success, false on failure.
     */
    public function update(string $key, $value) : bool
    {
        $autoload = $this->get_autoload_config($key);
        // Warn if no autoload config is set.
        if (null === $autoload) {
            $message = __('Updating an option without having an autoload value specified is discouraged.', 'ai-services');
            // phpcs:ignore Generic.Files.LineLength.TooLong
            $message .= ' ' . \sprintf(
                /* translators: 1: Method name, 2: Argument name, 3: Method name */
                __('Use the %1$s method or pass the "%2$s" argument to the %3$s to specify an autoload value.', 'ai-services'),
                // phpcs:ignore Generic.Files.LineLength.TooLong
                __CLASS__ . '::set_autoload_config()',
                'autoload',
                Option::class . '::__construct()'
            );
            _doing_it_wrong(__METHOD__, esc_html($message), '');
        }
        return (bool) update_option($key, $value, $autoload);
    }
    /**
     * Deletes the data for a given option from the database.
     *
     * @since n.e.x.t
     *
     * @param string $key Option key.
     * @return bool True on success, false on failure.
     */
    public function delete(string $key) : bool
    {
        return (bool) delete_option($key);
    }
    /**
     * Gets the autoload config for a given option in the database.
     *
     * @since n.e.x.t
     *
     * @param string $key Option key.
     * @return bool|null Whether or not the item should be autoloaded, or null if not specified.
     */
    public function get_autoload_config(string $key)
    {
        // The default value is true.
        return $this->autoload_config[$key] ?? null;
    }
    /**
     * Sets the autoload config for a given option in the database.
     *
     * @since n.e.x.t
     *
     * @param string $key      Option key.
     * @param bool   $autoload Option autoload config.
     */
    public function set_autoload_config(string $key, bool $autoload) : void
    {
        $this->autoload_config[$key] = $autoload;
    }
}
