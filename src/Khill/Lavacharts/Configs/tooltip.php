<?php namespace Khill\Lavacharts\Configs;
/**
 * Tooltip Properties Object
 *
 * An object containing all the values for the tooltip which can be passed
 * into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;

class tooltip extends configOptions
{
    /**
     * Show color code for the tooltip
     *
     * @var boolean
     */
    public $showColorCode = NULL;

    /**
     * Tooltip text style
     *
     * @var textStyle
     */
    public $textStyle = NULL;

    /**
     * Trigger Action of the tooltip.
     *
     * @var string
     */
    public $trigger = NULL;


    /**
     * Builds the tooltip object with specified options.
     *
     * @param array Configuration options for the tooltip
     * @return \tooltip
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
     * @param boolean State of showing the color code.
     * @return \tooltip
     */
    public function showColorCode($showColorCode)
    {
        if(is_bool($showColorCode))
        {
            $this->showColorCode = $showColorCode;
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * Sets the text style of the tooltip.
     *
     * @param textStyle Valid textStyle object.
     * @return \tooltip
     */
    public function textStyle($textStyle)
    {
        if(Helpers::is_textStyle($textStyle))
        {
            $this->textStyle = $textStyle->values();
        } else {
            $this->type_error(__FUNCTION__, 'object', 'class (textStyle)');
        }

        return $this;
    }

    /**
     * Sets The user interaction that causes the tooltip to be displayed.
     *
     * 'focus' - The tooltip will be displayed when the user hovers over an element.
     * 'none' - The tooltip will not be displayed.
     *
     * @param string Type of trigger, [ focus | none ].
     * @return \tooltip
     */
    public function trigger($trigger)
    {
        $values = array(
            'focus',
            'none'
        );

        if(in_array($trigger, $values))
        {
            $this->trigger = $trigger;
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

}
