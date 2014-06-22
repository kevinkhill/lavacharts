<?php namespace Khill\Lavacharts\Configs;

/**
 * Tooltip Properties Object
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
use Khill\Lavacharts\Configs\TextStyle;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Tooltip extends ConfigOptions
{
    /**
     * @var bool Show color code for the tooltip.
     */
    public $showColorCode = null;

    /**
     * @var TextStyle Tooltip text style
     */
    public $textStyle = null;

    /**
     * @var string Trigger Action of the tooltip.
     */
    public $trigger = null;


    /**
     * Builds the tooltip object with specified options.
     *
     * @param  array $config Configuration options for the tooltip
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigProperty
     *
     * @return Khill\Lavacharts\Configs\Tooltip
     */
    public function __construct($config = array())
    {
        $this->options = array(
            'showColorCode',
            'textStyle',
            'trigger'
        );

        parent::__construct($config);
    }

    /**
     * Sets whether to show the color code.
     *
     * @param  boolean $showColorCode State of showing the color code.
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     *
     * @return Khill\Lavacharts\Configs\Tooltip
     */
    public function showColorCode($showColorCode)
    {
        if (is_bool($showColorCode)) {
            $this->showColorCode = $showColorCode;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * Sets the text style of the tooltip.
     *
     * @param  Khill\Lavacharts\Configs\TextStyle $textStyle A valid TextStyle object.
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     *
     * @return Khill\Lavacharts\Configs\Tooltip
     */
    public function textStyle(TextStyle $textStyle)
    {
        $this->textStyle = $textStyle->getValues();

        return $this;
    }

    /**
     * Sets The user interaction that causes the tooltip to be displayed.
     *
     * 'focus' - The tooltip will be displayed when the user hovers over an element.
     * 'none'  - The tooltip will not be displayed.
     *
     * @param  string $trigger Type of trigger.
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     *
     * @return Khill\Lavacharts\Configs\Tooltip
     */
    public function trigger($trigger)
    {
        $values = array(
            'focus',
            'none'
        );

        if (in_array($trigger, $values)) {
            $this->trigger = $trigger;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'string',
                'with a value of '.Helpers::arrayToPipedString($values)
            );
        }

        return $this;
    }
}
