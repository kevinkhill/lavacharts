<?php namespace Khill\Lavacharts\Configs;

/**
 * BoxStyle Object
 *
 * For charts that support annotations, the boxStyle object controls the appearance
 * of the boxes surrounding annotations
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Configs
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/GPL-3.0 GPLv3
 */

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Configs\Gradient;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class BoxStyle extends ConfigOptions
{
    /**
     * @var string Color of the box outline.
     */
    public $stroke;

    /**
     * @var int|string Thickness of the box outline.
     */
    public $strokeWidth;

    /**
     * @var int|string X radius of the corner curvature.
     */
    public $rx;

    /**
     * @var int|string Y radius of the corner curvature.
     */
    public $ry;

    /**
     * @var Khill\Lavacharts\Configs\Gradient Attributes for linear gradient fill.
     */
    public $gradient;


    /**
     * Builds the boxStyle object with specified options
     *
     * @param  array $config
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return Khill\Lavacharts\Configs\BoxStyle
     */
    public function __construct($config = array())
    {
        if (! array_key_exists('stroke', $config)) {
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
     * @param string $stroke Valid HTML color.
     *
     * @return Khill\Lavacharts\Configs\BoxStyle
     */
    public function stroke($stroke)
    {
        if (is_string($stroke)) {
            $this->stroke = $stroke;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the thickness of the box outline.
     *
     * @param int|string $strokeWidth
     *
     * @return Khill\Lavacharts\Configs\BoxStyle
     */
    public function strokeWidth($strokeWidth)
    {
        if (is_numeric($strokeWidth)) {
            $this->strokeWidth = (int) $strokeWidth;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }

    /**
     * Sets the x-radius of the corner curvature.
     *
     * @param int|string $rx
     *
     * @return Khill\Lavacharts\Configs\BoxStyle
     */
    public function rx($rx)
    {
        if (is_numeric($rx)) {
            $this->rx = (int) $rx;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }

    /**
     * Sets the y-radius of the corner curvature.
     *
     * @param int|string $ry
     *
     * @return Khill\Lavacharts\Configs\BoxStyle
     */
    public function ry($ry)
    {
        if (is_numeric($ry)) {
            $this->ry = (int) $ry;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the attributes for linear gradient fill.
     *
     * @param Khill\Lavacharts\Configs\Gradient $gradient
     *
     * @return Khill\Lavacharts\Configs\BoxStyle
     */
    public function gradient(Gradient $gradient)
    {
        if (Helpers::isGradient($gradient)) {
            $this->gradient = $gradient;
        } else {
            throw new InvalidConfigValue(
                $this->className,
                __FUNCTION__,
                'string'
            );
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
     * @return string
     */
    private function _randomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
