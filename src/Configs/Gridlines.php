<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Animation ConfigObject
 *
 * An object containing all the values for the Animation which can
 * be passed into the chart's options.
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
class Gridlines extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Gridlines';

    /**
     * Default options for Gridlines
     *
     * @var array
     */
    private $defaults = [
        'color',
        'count',
        'units'
    ];

    /**
     * Builds the Gridlines object.
     *
     * @param  array $config Associative array containing key => value pairs for the various configuration options.
     * @return \Khill\Lavacharts\Configs\Gridlines
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Set the color of the gridlines.
     *
     * @param  string $color
     * @return \Khill\Lavacharts\Configs\Gridlines
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function color($color)
    {
        return $this->setStringOption(__FUNCTION__, $color);
    }

    /**
     * Sets the number of gridlines.
     *
     * @param  int $count
     * @return \Khill\Lavacharts\Configs\Gridlines
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function count($count)
    {
        if (is_int($count) === false || ($count < 2 && $count != -1)) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int',
                'with the value >= 2 or -1 for auto.'
            );
        }

        return $this->setOption(__FUNCTION__, $count);
    }

    /**
     * Overrides the default format for various aspects of date/datetime/timeofday data types.
     *
     * Allows formatting for years, months, days, hours, minutes, seconds, and milliseconds.
     *
     * @param  array $units
     * @return \Khill\Lavacharts\Configs\Gridlines
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function units($units)
    {
        $unitFormats = [];
        $unitValues  = [
            'years',
            'months',
            'days',
            'hours',
            'minutes',
            'seconds',
            'milliseconds'
        ];

        if (is_array($units) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        foreach ($units as $unit => $format) {
            if (is_string($unit) === false || in_array($unit, $unitValues) === false) {
                throw new InvalidConfigValue(
                    __FUNCTION__,
                    'string',
                    'Valid unit values are '.Utils::arrayToPipedString($unitValues)
                );
            }

            if (Utils::nonEmptyString($format) === false) {
                throw new InvalidConfigValue(
                    __FUNCTION__,
                    'string'
                );
            }

            $unitFormats[$unit] = $format;
        }

        return $this->setOption(__FUNCTION__, $unitFormats);
    }
}
