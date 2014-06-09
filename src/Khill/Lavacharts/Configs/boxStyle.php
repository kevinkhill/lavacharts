<?php namespace Khill\Lavacharts\Configs;
/**
 * boxStyle Object
 *
 * For charts that support annotations, the boxStyle object controls the appearance
 * of the boxes surrounding annotations
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use \Khill\Lavacharts\Helpers\Helpers;

class boxStyle extends configOptions
{
    /**
     * Color of the box outline.
     *
     * @var int
     */
    public $stroke;

    /**
     * Thickness of the box outline.
     *
     * @var int
     */
    public $strokeWidth;

    /**
     * X radius of the corner curvature.
     *
     * @var int
     */
    public $rx;

    /**
     * Y radius of the corner curvature.
     *
     * @var int
     */
    public $ry;

    /**
     * Attributes for linear gradient fill.
     *
     * @var gradient
     */
    public $gradient;


    /**
     * Builds the boxStyle object with specified options
     *
     * @param array config
     * @return \boxStyle
     */
    public function __construct($config = array())
    {
        if ( ! array_key_exists('stroke', $config))
        {
            $this->stroke = $this->_randomColor();
        }

        $this->options = array(
            'stroke',
            'strokeWidth',
            'rx',
            'ry',
            'gradient'
        );

        parent::__construct($config);
    }

    /**
     * If present, specifies the color for the box outline.
     * If undefined, a random color will be used.
     *
     * @param string color1
     * @return \boxStyle
     */
    public function stroke($stroke)
    {
        if(is_string($stroke))
        {
            $this->stroke = $stroke;
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Sets the thickness of the box outline.
     *
     * @param int strokeWidth
     * @return \boxStyle
     */
    public function strokeWidth($strokeWidth)
    {
        if(is_numeric($strokeWidth))
        {
            $this->strokeWidth = $strokeWidth;
        } else {
            $this->type_error(__FUNCTION__, 'numeric');
        }

        return $this;
    }

    /**
     * Sets the x-radius of the corner curvature.
     *
     * @param int rx
     * @return \boxStyle
     */
    public function rx($rx)
    {
        if(is_numeric($rx))
        {
            $this->rx = $rx;
        } else {
            $this->type_error(__FUNCTION__, 'numeric');
        }

        return $this;
    }

    /**
     * Sets the y-radius of the corner curvature.
     *
     * @param string ry
     * @return \boxStyle
     */
    public function ry($ry)
    {
        if(is_string($ry))
        {
            $this->ry = $ry;
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Sets the attributes for linear gradient fill.
     *
     * @param gradient Lava gradient object
     * @return \boxStyle
     */
    public function gradient($gradient)
    {
        if(Helpers::is_gradient($gradient))
        {
            $this->gradient = $gradient;
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Generates a random color in hex format.
     *
     * Thank you outis from stackoverflow for letting me be lazy with google
     * instead of coming up with this myself
     * http://stackoverflow.com/users/90527/outis
     *
     * @param void
     * @return string
     */
    private function _randomColor() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

}
