<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Crosshair ConfigObject
 *
 * An object containing the crosshair properties for the chart.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Crosshair extends JsonConfig
{
    /**
     * Common Methods
     */
    use \Khill\Lavacharts\Traits\ColorTrait;
    use \Khill\Lavacharts\Traits\OpacityTrait;

    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Crosshair';

    /**
     * Default options for Crosshair
     *
     * @var array
     */
    private $defaults = [
        'color',
        'focused',
        'opacity',
        'orientation',
        'selected',
        'trigger'
    ];

    /**
     * Stores all the information about the crosshair.
     *
     * All options can be set either by passing an array with associative
     * values for option => value, or by chaining together the functions
     * once an object has been created.
     *
     * @param  array $config
     * @return \Khill\Lavacharts\Configs\Crosshair
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * An object that specifies the crosshair focused color.
     *
     * @param  array $colorConfig
     * @return \Khill\Lavacharts\Configs\Crosshair
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function focused($colorConfig)
    {
        return $this->setOption(__FUNCTION__, new Color($colorConfig));
    }

    /**
     * The crosshair opacity, with 0.0 being fully transparent and 1.0 fully opaque.
     *
     * @param  float $opacity
     * @return \Khill\Lavacharts\Configs\Crosshair
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function opacity($opacity)
    {
        if (Utils::between(0.0, $opacity, 1.0) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $opacity);
    }

    /**
     * The crosshair orientation, which can be 'vertical' for vertical hairs only,
     * 'horizontal' for horizontal hairs only, or 'both' for traditional crosshairs.
     *
     * @param  string $orientation
     * @return \Khill\Lavacharts\Configs\Crosshair
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function orientation($orientation)
    {
        $values = [
            'both',
            'horizontal',
            'vertical'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $orientation, $values);
    }

    /**
     * An object that specifies the crosshair selected color.
     *
     * @param  array $colorConfig
     * @return \Khill\Lavacharts\Configs\Crosshair
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function selected($colorConfig)
    {
        return $this->setOption(__FUNCTION__, new Color($colorConfig));
    }

    /**
     * When to display crosshairs: on 'focus', 'selection', or 'both'.
     *
     * @param  string $trigger
     * @return \Khill\Lavacharts\Configs\Crosshair
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function trigger($trigger)
    {
        $values = [
            'both',
            'focus',
            'selection'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $trigger, $values);
    }
}
