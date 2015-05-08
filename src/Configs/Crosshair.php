<?php namespace Khill\Lavacharts\Configs;

/**
 * Crosshair Properties Object
 *
 * An object containing the crosshair properties for the chart.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Color;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Crosshair extends ConfigObject
{
    /**
     * Foreground color.
     *
     * @var string
     */
    public $color;

    /**
     * Focused color.
     *
     * @var
     */
    public $focused;

    /**
     * Crosshair opacity.
     *
     * @var float
     */
    public $opacity;

    /**
     * Crosshair orientation.
     *
     * @var string
     */
    public $orientation;

    /**
     * Focused color.
     *
     * @var
     */
    public $selected;

    /**
     * Crosshair trigger.
     *
     * @var string
     */
    public $trigger;

    /**
     * Stores all the information about the crosshair.
     *
     * All options can be set either by passing an array with associative
     * values for option => value, or by chaining together the functions
     * once an object has been created.
     *
     * @param  array                 $config
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return HorizontalAxis
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);

        $this->options = array_merge(
            $this->options,
            array(
                'color',
                'focused',
                'opacity',
                'orientation',
                'selected',
                'trigger'
            )
        );
    }

    /**
     * Specifies the crosshair color.
     *
     * @param  string             $color
     * @throws InvalidConfigValue
     * @return Crosshair
     */
    public function color($color)
    {
        if (is_string($color)) {
            $this->color = $color;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * An object that specifies the crosshair focused color.
     *
     * @param  Color     $color
     * @return Crosshair
     */
    public function focused(Color $color)
    {
        $this->focused = $color->getValues();

        return $this;
    }

    /**
     * The crosshair opacity, with 0.0 being fully transparent and 1.0 fully opaque.
     *
     * @param  float     $opacity
     * @return Crosshair
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

    /**
     * The crosshair orientation, which can be 'vertical' for vertical hairs only,
     * 'horizontal' for horizontal hairs only, or 'both' for traditional crosshairs.
     *
     * @param  string             $orientation
     * @throws InvalidConfigValue
     * @return Crosshair
     */
    public function orientation($orientation)
    {
        $values = [
            'both',
            'horizontal',
            'vertical'
        ];

        if (in_array($orientation, $values, true)) {
            $this->orientation = $orientation;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * An object that specifies the crosshair selected color.
     *
     * @param  Color     $color
     * @return Crosshair
     */
    public function selected(Color $color)
    {
        $this->selected = $color->getValues();

        return $this;
    }

    /**
     * When to display crosshairs: on 'focus', 'selection', or 'both'.
     *
     * @param  string             $trigger
     * @throws InvalidConfigValue
     * @return Crosshair
     */
    public function trigger($trigger)
    {
        $values = [
            'both',
            'focus',
            'selection'
        ];

        if (in_array($trigger, $values, true)) {
            $this->trigger = $trigger;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }
}
