<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Color Object
 *
 * Calendar charts use a striped diagonal pattern to indicate that there is no data for a particular day.
 * Use this object with backgroundColor and color options to override the grayscale defaults.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @since      2.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Color extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Color';

    /**
     * Default options for Color
     *
     * @var array
     */
    private $defaults = [
        'color',
        'backgroundColor',
        'opacity'
    ];

    /**
     * Builds the Color object with specified options
     *
     * @param  array $config
     * @return \Khill\Lavacharts\Configs\Color
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Specifies the foreground color.
     *
     * @param  string $fgColor
     * @return \Khill\Lavacharts\Configs\Color
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function color($fgColor)
    {
        return $this->setStringOption(__FUNCTION__, $fgColor);
    }

    /**
     * Specifies the background color.
     *
     * @param  string $bgColor
     * @return \Khill\Lavacharts\Configs\Color
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function backgroundColor($bgColor)
    {
        return $this->setStringOption(__FUNCTION__, $bgColor);
    }

    /**
     * Opacity, with 0.0 being fully transparent and 1.0 fully opaque.
     *
     * @param  float $opacity
     * @return \Khill\Lavacharts\Configs\Color
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function opacity($opacity)
    {
        if (Utils::between(0.0, $opacity, 1.0, true) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $opacity);
    }
}
