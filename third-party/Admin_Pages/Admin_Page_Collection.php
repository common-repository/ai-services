<?php

/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Admin_Page_Collection
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages;

use ArrayIterator;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts\Admin_Page;
use Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection;
use InvalidArgumentException;
use Traversable;
/**
 * Class for a collection of admin pages.
 *
 * @since n.e.x.t
 */
class Admin_Page_Collection implements Collection
{
    /**
     * Admin pages stored in the collection.
     *
     * @since n.e.x.t
     * @var Admin_Page[]
     */
    private $admin_pages = array();
    /**
     * Constructor.
     *
     * @since n.e.x.t
     *
     * @param Admin_Page[] $admin_pages List of admin page instances.
     *
     * @throws InvalidArgumentException Thrown when the given admin pages are invalid.
     */
    public function __construct(array $admin_pages)
    {
        foreach ($admin_pages as $admin_page) {
            if (!$admin_page instanceof Admin_Page) {
                throw new InvalidArgumentException(esc_html__('Invalid admin page provided for admin page collection.', 'ai-services'));
            }
            $this->admin_pages[] = $admin_page;
        }
    }
    /**
     * Returns an iterator for the collection.
     *
     * @since n.e.x.t
     *
     * @return ArrayIterator<int, Admin_Page> Collection iterator.
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->admin_pages);
    }
    /**
     * Returns the size of the collection.
     *
     * @since n.e.x.t
     *
     * @return int Collection size.
     */
    public function count() : int
    {
        return \count($this->admin_pages);
    }
}
