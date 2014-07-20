<?php namespace Khill\Lavacharts\Configs;

/**
 * Slice Properties Object
 *
 * An object containing all the values for the tooltip which can be passed
 * into the chart's options.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Configs
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Slice extends ConfigOptions
{
    /**
     * @var string The slice fill color.
     */
    public $color;

    /**
     * @var string Offset amount.
     */
    public $offset;

    /**
     * @var Khill\Lavacharts\Configs\TextStyle Slice text style.
     */
    public $textStyle;


    /**
     * Builds the slice object with specified options.
     *
     * @param  array Configuration options for the tooltip
     *
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     *
     * @return Khill\Lavacharts\Configs\Slice
     */
    public function __construct($config = array())
    {
        $this->className = 'Slice';
        $this->options = array(
            'color',
            'offset',
            'textStyle',
        );

        parent::__construct($config);
    }

    /**
     * The color to use for this slice. Specify a valid HTML color string.
     *
     * @param string
     *
     * @throws InvalidConfigValue
     *
     * @return Khill\Lavacharts\Configs\Slice
     */
    public function color($color)
    {
        if (is_string($color)) {
            $this->color = $color;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'as a valid HTML color code'
            );
        }

        return $this;
    }

    /**
     * How far to separate the slice from the rest of the pie.
     * from 0.0 (not at all) to 1.0 (the pie's radius).
     *
     * @param float offset
     *
     * @return Khill\Lavacharts\Configs\Slice
     */
    public function offset($offset)
    {
        if (Helpers::between(0.0, $offset, 1.0)) {
            $this->offset = $offset;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'float',
                'where 0.0 < $offset < 0.1'
            );
        }

        return $this;
    }

    /**
     * Overrides the global pieSliceTextSlice for this slice.
     *
     * @param Khill\Lavacharts\Configs\TextStyle $textStyle A valid textStyle object.
     *
     * @return Khill\Lavacharts\Configs\Slice
     */
    public function textStyle(TextStyle $textStyle)
    {
        $this->textStyle = $textStyle->getValues();

        return $this;
    }
}
