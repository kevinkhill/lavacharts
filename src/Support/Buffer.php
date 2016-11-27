<?php

namespace Khill\Lavacharts\Support;

use Khill\Lavacharts\Support\Contracts\JsonableInterface as Jsonable;

/**
 * Class Buffer
 *
 * Uses for building string outputs to send to the browser
 *
 * @package   Khill\Lavacharts\Support
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
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
     * @param string $str
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
        return (string) $this->contents;
    }

    /**
     * Returns the contents of the buffer when passed through json_encode
     *
     * @return string
     */
    public function toJson()
    {
        return (string) $this->contents;
    }

    /**
     * Sets the contents of the buffer
     *
     * @param string $str
     */
    public function setContents($str = '')
    {
        $this->contents = $str;
    }

    /**
     * Returns the contents of the buffer
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Append content to the end of the buffer
     *
     * @param  string $str
     * @return self
     */
    public function append($str)
    {
        $this->contents = $this->contents . $str;

        return $this;
    }

    /**
     * Prepend content to the beginning of the buffer
     *
     * @param  string $str
     * @return self
     */
    public function prepend($str)
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
    public function replace($search, $replace)
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
    public function pregReplace($search, $replace)
    {
        $this->contents = preg_replace($search, $replace, $this->contents);

        return $this;
    }
}
