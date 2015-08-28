<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Slice ConfigObject
 *
 * An object containing all the values for the tooltip which can be passed
 * into the chart's options.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Slice extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Slice';

    /**
     * Default options for Slices
     *
     * @var array
     */
    private $defaults = [
        'color',
        'offset',
        'textStyle'
    ];

    /**
     * Builds the slice object with specified options.
     *
     * @param  array $config Configuration options for the Slice
     * @return \Khill\Lavacharts\Configs\Slice
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * The color to use for this slice. Specify a valid HTML color string.
     *
     * @param  string $color
     * @return \Khill\Lavacharts\Configs\Slice
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function color($color)
    {
        return $this->setString(__FUNCTION__, $color);
    }

    /**
     * How far to separate the slice from the rest of the pie.
     * from 0.0 (not at all) to 1.0 (the pie's radius).
     *
     * @param  float $offset
     * @return \Khill\Lavacharts\Configs\Slice
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function offset($offset)
    {
        if (Utils::between(0.0, $offset, 1.0) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'float',
                'where 0.0 < $offset < 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $offset);
    }

    /**
     * Overrides the global pieSliceTextStyle for this slice.
     *
     * @param  array $textStyleConfig
     * @return \Khill\Lavacharts\Configs\Slice
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function textStyle($textStyleConfig)
    {
        return $this->setOption(__FUNCTION__, $textStyleConfig);
    }
}
