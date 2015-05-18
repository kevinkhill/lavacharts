<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Color Object
 *
 * Calendar charts use a striped diagonal pattern to indicate that there is no data for a particular day.
 * Use this object with backgroundColor and color options to override the grayscale defaults.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @since      2.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Color extends ConfigObject
{
    /**
     * Foreground color.
     *
     * @var string
     */
    public $color;

    /**
     * Background color.
     *
     * @var string
     */
    public $backgroundColor;

    /**
     * Opacity.
     *
     * @var float
     */
    public $opacity;

    /**
     * Builds the Color object with specified options
     *
     * @param  array                 $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function __construct($config = [])
    {
        parent::__construct($this, $config);
    }

    /**
     * Specifies the foreground color.
     *
     * @param  string             $fgColor
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function color($fgColor)
    {
        if (is_string($fgColor)) {
            $this->color = $fgColor;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Specifies the background color.
     *
     * @param  string             $bgColor
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function backgroundColor($bgColor)
    {
        if (is_string($bgColor)) {
            $this->backgroundColor = $bgColor;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Opacity, with 0.0 being fully transparent and 1.0 fully opaque.
     *
     * @param  float $opacity
     * @return self
     */
    public function opacity($opacity)
    {
        if (Utils::between(0.0, $opacity, 1.0, true)) {
            $this->opacity = $opacity;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this;
    }
}
