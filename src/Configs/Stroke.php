<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Stroke Object
 *
 * An object that specifies a the color, thickness and opacity of borders in charts.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @since      2.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Stroke extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Stroke';

    /**
     * Default options for TextStyles
     *
     * @var array
     */
    private $defaults = [
        'stroke',
        'strokeOpacity',
        'strokeWidth'
    ];

    /**
     * Builds the Stroke object with specified options
     *
     * @param  array $config
     * @return \Khill\Lavacharts\Configs\Stroke
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Sets the color of the stroke.
     *
     * @param  string $stroke A valid html color string
     * @return \Khill\Lavacharts\Configs\Stroke
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function stroke($stroke)
    {
        return $this->setStringOption(__FUNCTION__, $stroke);
    }

    /**
     * Sets the opacity of the stroke.
     *
     * @param  float $strokeOpacity
     * @return \Khill\Lavacharts\Configs\Stroke
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function strokeOpacity($strokeOpacity)
    {
        if (Utils::between(0.0, $strokeOpacity, 1.0, true) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 and 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $strokeOpacity);
    }

    /**
     * Sets the width of the stroke.
     *
     * @param  int $strokeWidth
     * @return \Khill\Lavacharts\Configs\Stroke
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function strokeWidth($strokeWidth)
    {
        return $this->setIntOption(__FUNCTION__, $strokeWidth);
    }
}
