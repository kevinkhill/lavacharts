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
 * @package    Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class MagnifyingGlass extends ConfigObject
{
    /**
     * Enabled state of the magnifying glass.
     *
     * @var bool
     */
    public $enable = true;

    /**
     * Zoom factor of the magnifying glass.
     *
     * @var int
     */
    public $zoomFactor;


    /**
     * Builds the MagnifyingGlass object.
     *
     * If created with no parameter, it defaults to enabled with a zoom factor
     * of 5. Passing a number in upon creation, then the zoomFactor will be set.
     *
     * @param  bool                  $zoomFactor
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return MagnifyingGlass
     */
    public function __construct($zoomFactor = 5)
    {
        $this->zoomFactor($zoomFactor);

        parent::__construct($this, array('zoomFactor' => $zoomFactor));
    }

    /**
     * The zoom factor of the magnifying glass.
     *
     * @param  int             $zoomFactor Can be any number greater than 0.
     * @return MagnifyingGlass
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
