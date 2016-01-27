<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Horizontal Axis ConfigObject
 *
 * An object containing all the values for the axis which can be
 * passed into the chart's options.
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
class HorizontalAxis extends Axis
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'HorizontalAxis';

    /**
     * Default options for HorizontalAxis
     *
     * @var array
     */
    private $extDefaults = [
        'allowContainerBoundaryTextCutoff',
        'slantedText',
        'slantedTextAngle'
    ];

    /**
     * Stores all the information about the horizontal axis. All options can be
     * set either by passing an array with associative values for option =>
     * value, or by chaining together the functions once an object has been
     * created.
     *
     * @param  array $config
     * @return \Khill\Lavacharts\Configs\HorizontalAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->extDefaults);

        parent::__construct($options, $config);
    }

    /**
     * Sets whether the container can cutoff the labels or not.
     *
     * If false, will hide outermost labels rather than allow them to be
     * cropped by the chart container. If true, will allow label cropping.
     *
     * This option is only supported for a discrete axis.
     *
     * @param  boolean $cutoff Status of allowing label cutoff
     * @return \Khill\Lavacharts\Configs\HorizontalAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function allowContainerBoundaryTextCutoff($cutoff)
    {
        return $this->setBoolOption(__FUNCTION__, $cutoff);
    }

    /**
     * Sets whether the labels are slanted or not.
     * If true, draw the axis text at an angle, to help fit more text
     * along the axis; if false, draw axis text upright. Default
     * behavior is to slant text if it cannot all fit when drawn upright.
     * Notice that this option is available only when the $this->textPosition is
     * set to 'out' (which is the default).
     * This option is only supported for a discrete axis.
     *
     * @param  boolean $slant Status of label slant
     * @return \Khill\Lavacharts\Configs\HorizontalAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    public function slantedText($slant)
    {
        if (is_bool($slant) === false || $this->options->get('textPosition') != 'out') {
            throw new InvalidConfigValue(
                self::TYPE . '->' . __FUNCTION__,
                'bool',
                'and textPosition must be "out"'
            );
        }

        return $this->setBoolOption(__FUNCTION__, $slant);
    }

    /**
     * Sets the angle of the axis text, if it's drawn slanted.
     * Ignored if "slantedText" is false, or is in auto mode, and the chart decided to
     * draw the text horizontally. This option is only supported for a discrete axis.
     *
     * @param  int $angle Angle of labels
     * @return \Khill\Lavacharts\Configs\HorizontalAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function slantedTextAngle($angle)
    {
        if (is_int($angle) === false || Utils::between(1, $angle, 90) == false) {
            throw new InvalidConfigValue(
                self::TYPE . '->' . __FUNCTION__,
                'int',
                'between 1 - 90'
            );
        }

        return $this->setOption(__FUNCTION__, $angle);
    }
}
