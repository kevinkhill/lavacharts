<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * Tooltip ConfigObject
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
class Tooltip extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Tooltip';

    /**
     * Default options for Tooltips
     *
     * @var array
     */
    private $defaults = [
        'isHtml',
        'showColorCode',
        'textStyle',
        'trigger'
    ];

    /**
     * Builds the tooltip object with specified options.
     *
     * @param  array $config Configuration options for the tooltip
     * @return \Khill\Lavacharts\Configs\Tooltip
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

	/**
     * Sets whether the tooltip is HTML.
     *
     * @param  bool $isHtml
     * @return \Khill\Lavacharts\Configs\Tooltip
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function isHtml($isHtml)
    {
        return $this->setBoolOption(__FUNCTION__, $isHtml);
    }

    /**
     * Sets whether to show the color code.
     *
     * @param  bool $showColorCode State of showing the color code.
     * @return \Khill\Lavacharts\Configs\Tooltip
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function showColorCode($showColorCode)
    {
        return $this->setBoolOption(__FUNCTION__, $showColorCode);
    }

    /**
     * Sets the text style of the tooltip.
     *
     * @param  array $textStyleConfig
     * @return \Khill\Lavacharts\Configs\Tooltip
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function textStyle($textStyleConfig)
    {
        return $this->setOption(__FUNCTION__, new TextStyle($textStyleConfig));
    }

    /**
     * Sets the user interaction that causes the tooltip to be displayed.
     *
     * 'focus' - The tooltip will be displayed when the user hovers over an element.
     * 'none'  - The tooltip will not be displayed.
     *
     * @param  string $trigger Type of trigger.
     * @return \Khill\Lavacharts\Configs\Tooltip
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function trigger($trigger)
    {
        $values = [
            'focus',
            'none'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $trigger, $values);
    }
}
