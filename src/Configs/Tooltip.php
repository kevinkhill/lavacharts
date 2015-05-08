<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Tooltip ConfigObject
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
class Tooltip extends ConfigObject
{
    /**
     * Show color code for the tooltip.
     *
     * @var bool
     */
    public $showColorCode;

    /**
     * Tooltip text style
     *
     * @var TextStyle
     */
    public $textStyle;

    /**
     * Trigger Action of the tooltip.
     *
     * @var string
     */
    public $trigger;


    /**
     * Builds the tooltip object with specified options.
     *
     * @param  array                 $config Configuration options for the tooltip
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Sets whether to show the color code.
     *
     * @param  bool               $showColorCode State of showing the color code.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function showColorCode($showColorCode)
    {
        if (is_bool($showColorCode)) {
            $this->showColorCode = $showColorCode;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * Sets the text style of the tooltip.
     *
     * @param  TextStyle $textStyle A valid TextStyle object.
     * @return self
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
     * @param  string             $trigger Type of trigger.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
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
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }
}
