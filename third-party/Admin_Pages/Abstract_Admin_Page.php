<?php

/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Abstract_Admin_Page
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages;

use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts\Admin_Page;
/**
 * Base class representing a WordPress admin page.
 *
 * @since n.e.x.t
 */
abstract class Abstract_Admin_Page implements Admin_Page
{
    /**
     * Admin page slug.
     *
     * @since n.e.x.t
     * @var string
     */
    private $slug;
    /**
     * Admin page title.
     *
     * @since n.e.x.t
     * @var string
     */
    private $title;
    /**
     * Admin page capability.
     *
     * @since n.e.x.t
     * @var string
     */
    private $capability;
    /**
     * Constructor.
     *
     * @since n.e.x.t
     */
    public function __construct()
    {
        $this->slug = $this->slug();
        $this->title = $this->title();
        $this->capability = $this->capability();
    }
    /**
     * Gets the admin page slug.
     *
     * @since n.e.x.t
     *
     * @return string Admin page slug.
     */
    public final function get_slug() : string
    {
        return $this->slug;
    }
    /**
     * Gets the admin page title.
     *
     * @since n.e.x.t
     *
     * @return string Admin page title.
     */
    public final function get_title() : string
    {
        return $this->title;
    }
    /**
     * Gets the admin page's required capability.
     *
     * @since n.e.x.t
     *
     * @return string Admin page capability.
     */
    public final function get_capability() : string
    {
        return $this->capability;
    }
    /**
     * Initializes functionality for the admin page.
     *
     * @since n.e.x.t
     */
    public abstract function load() : void;
    /**
     * Renders the admin page.
     *
     * @since n.e.x.t
     */
    public abstract function render() : void;
    /**
     * Returns the admin page slug.
     *
     * @since n.e.x.t
     *
     * @return string Admin page slug.
     */
    protected abstract function slug() : string;
    /**
     * Returns the admin page title.
     *
     * @since n.e.x.t
     *
     * @return string Admin page title.
     */
    protected abstract function title() : string;
    /**
     * Returns the admin page's required capability.
     *
     * @since n.e.x.t
     *
     * @return string Admin page capability.
     */
    protected abstract function capability() : string;
}
