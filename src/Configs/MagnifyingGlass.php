<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * MagnifyingGlass Object
 *
 * An object containing all the values for the magnifying glass which can
 * be passed into the chart's options.
 * If created with no parameter, it defaults to enabled with a zoom factor
 * of 5.
 * Passing a number in upon creation, then the zoomFactor will be set.
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
class MagnifyingGlass extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'MagnifyingGlass';

    /**
     * Default options for MagnifyingGlass
     *
     * @var array
     */
    private $defaults = [
        'enable',
        'zoomFactor'
    ];

    /**
     * Builds the MagnifyingGlass object.
     *
     * If created with no parameter, it defaults to enabled with a zoom factor
     * of 5. Passing a number in upon creation, then the zoomFactor will be set.
     *
     * @param  int $zoomFactor
     * @return \Khill\Lavacharts\Configs\MagnifyingGlass
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($zoomFactor = 5)
    {
        $options = new Options($this->defaults);

        parent::__construct($options, [
            'enable' => true,
            'zoomFactor' => $zoomFactor
        ]);
    }

    /**
     * The zoom factor of the magnifying glass.
     *
     * @param  integer $zoomFactor Can be any number greater than 0.
     * @return \Khill\Lavacharts\Configs\MagnifyingGlass
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function zoomFactor($zoomFactor)
    {
        if (is_int($zoomFactor) === false || $zoomFactor <= 0) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int',
                'greater than 0'
            );
        }

        return $this->setOption(__FUNCTION__, $zoomFactor);
    }
}
