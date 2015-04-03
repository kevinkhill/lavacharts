<?php namespace Khill\Lavacharts\Configs;

/**
 * Gradient Object
 *
 * An object that specifies a color gradient
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Gradient extends ConfigObject
{
    /**
     * Start color for gradient.
     *
     * @var int
     */
    public $color1;

    /**
     * Finish color for gradient.
     *
     * @var int
     */
    public $color2;

    /**
     * Where on the boundary to start in X.
     *
     * @var array
     */
    public $x1;

    /**
     * Where on the boundary to start in Y.
     *
     * @var array
     */
    public $y1;

    /**
     * Where on the boundary to finish, relative to $x1.
     *
     * @var array
     */
    public $x2;

    /**
     * Where on the boundary to finish, relative to $y1.
     *
     * @var array
     */
    public $y2;

    /**
     * Builds the gradient object with specified options
     *
     * @param  array                 $config
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return Gradient
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * If present, specifies the start color for the gradient.
     *
     * @param  string   $color1
     * @return Gradient
     */
    public function color1($color1)
    {
        if (is_string($color1)) {
            $this->color1 = $color1;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * If present, specifies the finish color for the gradient.
     *
     * @param  string   $color2
     * @return Gradient
     */
    public function color2($color2)
    {
        if (is_string($color2)) {
            $this->color2 = $color2;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets where on the boundary to start in X.
     *
     * @param  string   $x1
     * @return Gradient
     */
    public function x1($x1)
    {
        if (is_string($x1)) {
            $this->x1 = $x1;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets where on the boundary to start in Y.
     *
     * @param  string   $y1
     * @return Gradient
     */
    public function y1($y1)
    {
        if (is_string($y1)) {
            $this->y1 = $y1;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets where on the boundary to end in X, relative to x1.
     *
     * @param  string   $x2
     * @return Gradient
     */
    public function x2($x2)
    {
        if (is_string($x2)) {
            $this->x2 = $x2;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets where on the boundary to end in Y, relative to y1.
     *
     * @param  string   $y2
     * @return Gradient
     */
    public function y2($y2)
    {
        if (is_string($y2)) {
            $this->y2 = $y2;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }
}
