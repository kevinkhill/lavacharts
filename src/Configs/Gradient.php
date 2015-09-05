<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * Gradient ConfigObject
 *
 * An object that specifies a color gradient
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
class Gradient extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Gradient';

    /**
     * Default options for Gradient
     *
     * @var array
     */
    private $defaults = [
        'color1',
        'color2',
        'x1',
        'y1',
        'x2',
        'y2'
    ];

    /**
     * Builds the gradient object with specified options
     *
     * @param  array $config
     * @return \Khill\Lavacharts\Configs\Gradient
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * If present, specifies the start color for the gradient.
     *
     * @param  string $color1
     * @return \Khill\Lavacharts\Configs\Gradient
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function color1($color1)
    {
        return $this->setStringOption(__FUNCTION__, $color1);
    }

    /**
     * If present, specifies the finish color for the gradient.
     *
     * @param  string $color2
     * @return \Khill\Lavacharts\Configs\Gradient
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function color2($color2)
    {
        return $this->setStringOption(__FUNCTION__, $color2);
    }

    /**
     * Sets where on the boundary to start in X.
     *
     * @param  string $x1
     * @return \Khill\Lavacharts\Configs\Gradient
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function x1($x1)
    {
        return $this->setStringOption(__FUNCTION__, $x1);
    }

    /**
     * Sets where on the boundary to start in Y.
     *
     * @param  string $y1
     * @return \Khill\Lavacharts\Configs\Gradient
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function y1($y1)
    {
        return $this->setStringOption(__FUNCTION__, $y1);
    }

    /**
     * Sets where on the boundary to end in X, relative to x1.
     *
     * @param  string $x2
     * @return \Khill\Lavacharts\Configs\Gradient
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function x2($x2)
    {
        return $this->setStringOption(__FUNCTION__, $x2);
    }

    /**
     * Sets where on the boundary to end in Y, relative to y1.
     *
     * @param  string $y2
     * @return \Khill\Lavacharts\Configs\Gradient
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function y2($y2)
    {
        return $this->setStringOption(__FUNCTION__, $y2);
    }
}
