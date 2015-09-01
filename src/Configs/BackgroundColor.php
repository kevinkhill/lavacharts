<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * backgroundColor Object
 *
 * An object containing all the values for the backgroundColor object which can
 * be passed into the chart's options.
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
class BackgroundColor extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'BackgroundColor';

    /**
     * Default options for BackgroundColor
     *
     * @var array
     */
    private $defaults = [
        'fill',
        'stroke',
        'strokeWidth'
    ];

    /**
     * Builds the backgroundColor object with specified options
     *
     * Pass an associative array with values for the keys
     * [ stroke | strokeWidth | fill ]
     *
     * @param  array $config Configuration options
     * @return \Khill\Lavacharts\Configs\BackgroundColor
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Sets the chart color fill.
     *
     * Example: 'blue' or '#C5C5C5'
     *
     * @param  string $fill Valid HTML color string.
     * @return \Khill\Lavacharts\Configs\BackgroundColor
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function fill($fill)
    {
        return $this->setStringOption(__FUNCTION__, $fill);
    }

    /**
     * Sets the chart border color.
     *
     * Example: 'red' or '#A2A2A2'
     *
     * @param  string $stroke Valid HTML color string.
     * @return \Khill\Lavacharts\Configs\BackgroundColor
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function stroke($stroke)
    {
        return $this->setStringOption(__FUNCTION__, $stroke);
    }

    /**
     * Sets the chart border width.
     *
     * @param  integer $strokeWidth Border width, in pixels.
     * @return \Khill\Lavacharts\Configs\BackgroundColor
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function strokeWidth($strokeWidth)
    {
        return $this->setIntOption(__FUNCTION__, $strokeWidth);
    }
}
