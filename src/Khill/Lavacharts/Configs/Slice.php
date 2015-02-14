<?php namespace Khill\Lavacharts\Configs;

/**
 * Slice Properties Object
 *
 * An object containing all the values for the tooltip which can be passed
 * into the chart's options.
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

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Slice extends ConfigObject
{
    /**
     * The slice fill color.
     *
     * @var string
     */
    public $color;

    /**
     * Offset amount.
     *
     * @var string
     */
    public $offset;

    /**
     * Slice text style.
     *
     * @var TextStyle
     */
    public $textStyle;


    /**
     * Builds the slice object with specified options.
     *
     * @param  array                 $config Configuration options for the Slice
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return Slice
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * The color to use for this slice. Specify a valid HTML color string.
     *
     * @param  string             $color
     * @throws InvalidConfigValue
     * @return Slice
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
     * @param  float              $offset
     * @throws InvalidConfigValue
     * @return Slice
     */
    public function offset($offset)
    {
        if (Utils::between(0.0, $offset, 1.0)) {
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
     * @param  TextStyle $textStyle A valid textStyle object.
     * @return Slice
     */
    public function textStyle(TextStyle $textStyle)
    {
        $this->textStyle = $textStyle->getValues();

        return $this;
    }
}
