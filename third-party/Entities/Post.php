<?php

/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity;
use WP_Post;
/**
 * Class representing a WordPress post.
 *
 * @since n.e.x.t
 */
class Post implements Entity
{
    /**
     * The underlying WordPress post object.
     *
     * @since n.e.x.t
     * @var WP_Post
     */
    private $wp_obj;
    /**
     * Constructor.
     *
     * @since n.e.x.t
     *
     * @param WP_Post $post The underlying WordPress post object.
     */
    public function __construct(WP_Post $post)
    {
        $this->wp_obj = $post;
    }
    /**
     * Gets the post ID.
     *
     * @since n.e.x.t
     *
     * @return int The post ID.
     */
    public function get_id() : int
    {
        return (int) $this->wp_obj->ID;
    }
    /**
     * Checks whether the post is publicly accessible.
     *
     * @since n.e.x.t
     *
     * @return bool True if the post is public, false otherwise.
     */
    public function is_public() : bool
    {
        return is_post_publicly_viewable($this->wp_obj);
    }
    /**
     * Gets the post's primary URL.
     *
     * @since n.e.x.t
     *
     * @return string Post permalink, or empty string if none.
     */
    public function get_url() : string
    {
        return (string) get_permalink($this->wp_obj);
    }
    /**
     * Gets the post's edit URL, if the current user is able to edit it.
     *
     * @since n.e.x.t
     *
     * @return string URL to edit the post, or empty string if unable to edit.
     */
    public function get_edit_url() : string
    {
        return (string) get_edit_post_link($this->wp_obj, 'raw');
    }
    /**
     * Gets the value for the given field of the post.
     *
     * @since n.e.x.t
     *
     * @param string $field The field identifier.
     * @return mixed Value for the field, `null` if not set.
     */
    public function get_field_value(string $field)
    {
        switch ($field) {
            // Post status has special handling.
            case 'post_status':
                return get_post_status($this->wp_obj);
            default:
                return $this->wp_obj->{$field} ?? null;
        }
    }
}
