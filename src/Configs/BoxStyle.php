<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * BoxStyle Object
 *
 * For charts that support annotations, the boxStyle object controls the appearance
 * of the boxes surrounding annotations
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
class BoxStyle extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'BoxStyle';

    /**
     * Default options for BoxStyle
     *
     * @var array
     */
    private $defaults = [
        'stroke',
        'strokeWidth',
        'rx',
        'ry',
        'gradient'
    ];

    /**
     * Builds the boxStyle object with specified options
     *
     * @param  array $config
     * @return \Khill\Lavacharts\Configs\BoxStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * If present, specifies the color for the box outline.
     * If undefined, a random color will be used.
     *
     * @param  string $stroke Valid HTML color.
     * @return \Khill\Lavacharts\Configs\BoxStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function stroke($stroke)
    {
        return $this->setStringOption(__FUNCTION__, $stroke);
    }

    /**
     * Sets the thickness of the box outline.
     *
     * @param  integer|string $strokeWidth
     * @return \Khill\Lavacharts\Configs\BoxStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function strokeWidth($strokeWidth)
    {
        return $this->setIntOption(__FUNCTION__, $strokeWidth);
    }

    /**
     * Sets the x-radius of the corner curvature.
     *
     * @param  integer|string $rx
     * @return \Khill\Lavacharts\Configs\BoxStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function rx($rx)
    {
        return $this->setIntOption(__FUNCTION__, $rx);
    }

    /**
     * Sets the y-radius of the corner curvature.
     *
     * @param  integer|string $ry
     * @return \Khill\Lavacharts\Configs\BoxStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function ry($ry)
    {
        return $this->setIntOption(__FUNCTION__, $ry);
    }

    /**
     * Sets the attributes for linear gradient fill.
     *
     * @param  array $gradientConfig
     * @return \Khill\Lavacharts\Configs\BoxStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function gradient($gradientConfig)
    {
        return $this->setOption(__FUNCTION__, new Gradient($gradientConfig));
    }
}
