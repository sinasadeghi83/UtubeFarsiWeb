<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Streaming;

class RepsCollection implements \Countable, \IteratorAggregate
{
    /** @return array  */
    private $representations = [];

    /**
     * RepsCollection constructor.
     */
    public function __construct(array $representations = [])
    {
        array_walk($representations, [$this, 'add']);
    }

    /**
     * @return null|Representation
     */
    public function first()
    {
        return reset($this->representations) ?: null;
    }

    /**
     * @return null|Representation
     */
    public function end()
    {
        return end($this->representations) ?: null;
    }

    /**
     * @return RepsCollection
     */
    public function add(RepresentationInterface $representation)
    {
        array_push($this->representations, $representation);

        return $this;
    }

    public function all(): array
    {
        return $this->representations;
    }

    public function keys(): array
    {
        return array_keys($this->representations);
    }

    /**
     * @param null  $default
     * @param mixed $key
     *
     * @return mixed|Representation
     */
    public function get($key, $default = null)
    {
        return $this->representations[$key] ?? $default;
    }

    public function map(callable $func)
    {
        $this->representations = array_map($func, $this->representations);
    }

    /**
     * count of representations.
     */
    public function count(): int
    {
        return count($this->representations);
    }

    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->representations);
    }
}
