<?php namespace Khill\Lavacharts\Configs;
/**
 * magnifyingGlass Properties Object
 *
 * An object containing all the values for the magnifying glass which can
 * be passed into the chart's options.
 *
 * If created with no parameter, it defaults to enabled with a zoom factor
 * of 5.
 *
 * Passing a number in upon creation, then the zoomFactor will be set.
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

class magnifyingGlass extends configOptions
{
    /**
     * The magnifying glass enabled state.
     */
    private $enable = TRUE;

    /**
     * Zoom factor of the magnifying glass.
     *
     * @var string
     */
    public $zoomFactor = 5;

    /**
     * Builds the magnifyingGlass object.
     *
     * If created with no parameter, it defaults to enabled with a zoom factor
     * of 5.
     *
     * Passing a number in upon creation, then the zoomFactor will be set.
     *
     * @param zoomfactor
     * @return \tooltip
     */
    public function __construct($zoomFactor = NULL)
    {
        $this->options = array('zoomFactor');

        if(is_null($zoomFactor))
        {
            parent::__construct(array());
        } else {
            parent::__construct(array('zoomFactor' => $zoomFactor));
        }
    }

    /**
     * The zoom factor of the magnifying glass. Can be any number greater than 0.
     *
     * @param int $zoomFactor
     * @return \magnifyingGlass
     */
    public function zoomFactor($zoomFactor)
    {
        if(is_numeric($zoomFactor) && $zoomFactor > 0)
        {
            $this->zoomFactor = $zoomFactor;
        } else {
            $this->type_error(__FUNCTION__, 'int', 'greater than 0');
        }

        return $this;
    }

}
