<?php namespace Khill\Lavacharts\Configs;
/**
 * Tooltip Properties Object
 *
 * An object containing all the values for the tooltip which can be passed
 * into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class slice extends configOptions
{
    /**
     * The slice fill color.
     *
     * @var string Valid HTML color.
     */
    public $color = null;

    /**
     * Offset amount.
     *
     * @var string
     */
    public $offset = null;

    /**
     * Slice text style.
     *
     * @var textStyle
     */
    public $textStyle = null;


    /**
     * Builds the slice object with specified options.
     *
     * @param array Configuration options for the tooltip
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return \tooltip
     */
    public function __construct($config = array())
    {
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
     * @return \slice
     */
    public function color($color)
    {
        if(is_string($color))
        {
            $this->color = $color;
        } else {
            throw new InvalidConfigValue($this->className, __FUNCTION__, 'string', 'as a valid HTML color code');
        }

        return $this;
    }

    /**
     * How far to separate the slice from the rest of the pie.
     * from 0.0 (not at all) to 1.0 (the pie's radius).
     *
     * @param float offset
     * @return \slice
     */
    public function offset($offset)
    {
        if(is_float($offset) && Helpers::between(0.0, $offset, 1.0))
        {
            $this->offset = $offset;
        } else {
            throw new InvalidConfigValue($this->className, __FUNCTION__, 'float', 'where 0.0 < $offset < 0.1');
        }

        return $this;
    }

    /**
     * Overrides the global pieSliceTextSlice for this slice.
     *
     * @param textStyle Valid textStyle object.
     * @return \slice
     */
    public function textStyle($textStyle)
    {
        if(Helpers::is_textStyle($textStyle))
        {
            $this->textStyle = $textStyle->values();
        } else {
            throw new InvalidConfigValue($this->className, __FUNCTION__, 'textStyle');
        }

        return $this;
    }

}
