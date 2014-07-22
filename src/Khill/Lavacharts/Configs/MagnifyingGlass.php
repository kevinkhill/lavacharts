<?php namespace Khill\Lavacharts\Configs;

/**
 * MagnifyingGlass Properties Object
 *
 * An object containing all the values for the magnifying glass which can
 * be passed into the chart's options.
 * If created with no parameter, it defaults to enabled with a zoom factor
 * of 5.
 * Passing a number in upon creation, then the zoomFactor will be set.
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

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class MagnifyingGlass extends ConfigOptions
{
    /**
     * @var boolean Enabled state of the magnifying glass.
     */
    public $enable = true;

    /**
     * @var int Zoom factor of the magnifying glass.
     */
    public $zoomFactor;


    /**
     * Builds the MagnifyingGlass object.
     *
     * If created with no parameter, it defaults to enabled with a zoom factor
     * of 5. Passing a number in upon creation, then the zoomFactor will be set.
     *
     * @param boolean $zoomFactor
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigProperty
     *
     * @return Khill\Lavacharts\Configs\MagnifyingGlass
     */
    public function __construct($zoomFactor = 5)
    {
        $this->zoomFactor($zoomFactor);

        parent::__construct($this, array('zoomFactor' => $zoomFactor));
    }

    /**
     * The zoom factor of the magnifying glass.
     *
     * @param int $zoomFactor Can be any number greater than 0.
     *
     * @return Khill\Lavacharts\Configs\MagnifyingGlass
     */
    public function zoomFactor($zoomFactor)
    {
        if (is_numeric($zoomFactor) && $zoomFactor > 0) {
            $this->zoomFactor = $zoomFactor;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int',
                'greater than 0'
            );
        }

        return $this;
    }
}
