<?php

/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */
namespace Felix_Arntz\AI_Services_Dependencies\Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

use Countable;
use IteratorAggregate;
use Traversable;
/**
 * Interface for a collection.
 *
 * @since n.e.x.t
 */
interface Collection extends IteratorAggregate, Countable
{
    /**
     * Returns an iterator for the collection.
     *
     * @since n.e.x.t
     *
     * @return Traversable Collection iterator.
     */
    public function getIterator() : Traversable;
    /* @phpstan-ignore-line */
    /**
     * Returns the size of the collection.
     *
     * @since n.e.x.t
     *
     * @return int Collection size.
     */
    public function count() : int;
}
