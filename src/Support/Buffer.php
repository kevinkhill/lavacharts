<?php

namespace Khill\Lavacharts\Support;

use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Support\Contracts\Jsonable as Jsonable;

/**
 * Class Buffer
 *
 * Uses for building string outputs to send to the browser
 *
 * @package   Khill\Lavacharts\Support
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Buffer implements Jsonable
{
    /**
     * Contents of the buffer
     *
     * @var string
     */
    public $contents;

    /**
     * Buffer constructor.
     *
     * @param string|mixed $str
     */
    public function __construct($str = '')
    {
        $this->setContents($str);
    }

    /**
     * Returns the contents of the buffer when accessed as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->contents;
    }

    /**
     * Returns the contents of the buffer when passed through json_encode
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->__toString());
    }

    /**
     * Custom serialization of the chart.
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    /**
     * Sets the contents of the buffer
     *
     * @param string|mixed $object
     * @throws InvalidArgumentException
     */
    public function setContents(string $object)
    {
//        if (! is_string($object) && ! method_exists($object, '__toString')) {
//            throw new InvalidArgumentException(
//                $object,
//                'string or objects implementing __toString'
//            );
//        }

        $this->contents = (string) $object;
    }

    /**
     * Returns the contents of the buffer
     *
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * Append content to the end of the buffer
     *
     * @param  string|Buffer $str
     * @return self
     */
    public function append(string $str): self
    {
        $this->contents = $this->contents . $str;

        return $this;
    }

    /**
     * Prepend content to the beginning of the buffer
     *
     * @param  string|Buffer $str
     * @return self
     */
    public function prepend(string $str): self
    {
        $this->contents = $str . $this->contents;

        return $this;
    }

    /**
     * Find and replace content in the buffer using str_replace
     *
     * @param  string $search
     * @param  string $replace
     * @return self
     */
    public function replace(string $search, string $replace): self
    {
        $this->contents = str_replace($search, $replace, $this->contents);

        return $this;
    }

    /**
     * Find and replace content in the buffer using preg_replace
     *
     * @param  string $search
     * @param  string $replace
     * @return self
     */
    public function pregReplace(string $search, string $replace): self
    {
        $this->contents = preg_replace($search, $replace, $this->contents);

        return $this;
    }
}
