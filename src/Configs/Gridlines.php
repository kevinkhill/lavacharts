<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Animation ConfigObject
 *
 * An object containing all the values for the Animation which can
 * be passed into the chart's options.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @since      2.2.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Gridlines extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Gridlines';

    /**
     * Default options for TextStyles
     *
     * @var array
     */
    private $defaults = [
        'color',
        'count'
    ];

    /**
     * Builds the Animation object.
     *
     * @param  array $config Associative array containing key => value pairs for the various configuration options.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     *
     * @param  string $duration
     * @return self
     */
    public function color($color)
    {
        return $this->setStringOption(__FUNCTION__, $color);
    }

    /**
     *
     * @param  int $count
     * @return self
     */
    public function count($count)
    {
        if (is_int($count) === false && $count < 2 || $count != -1) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int',
                'with the value of the == -1 || >= 2'
            );
        }

        return $this->setIntOption(__FUNCTION__, $count);
    }
}
