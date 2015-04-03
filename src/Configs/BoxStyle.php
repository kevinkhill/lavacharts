<?php namespace Khill\Lavacharts\Configs;

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

use Khill\Lavacharts\Exceptions\InvalidConfigProperty;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class BoxStyle extends ConfigObject
{
    /**
     * Color of the box outline.
     *
     * @var string
     */
    public $stroke;

    /**
     * Thickness of the box outline.
     *
     * @var int|string
     */
    public $strokeWidth;

    /**
     * X radius of the corner curvature.
     *
     * @var int|string
     */
    public $rx;

    /**
     * Y radius of the corner curvature.
     *
     * @var int|string
     */
    public $ry;

    /**
     * Attributes for linear gradient fill.
     *
     * @var Gradient
     */
    public $gradient;

    /**
     * Builds the boxStyle object with specified options
     *
     * @param array $config
     *
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return BoxStyle
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * If present, specifies the color for the box outline.
     * If undefined, a random color will be used.
     *
     * @param  string   $stroke Valid HTML color.
     * @return BoxStyle
     */
    public function stroke($stroke)
    {
        if (is_string($stroke)) {
            $this->stroke = $stroke;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the thickness of the box outline.
     *
     * @param  int|string $strokeWidth
     * @return BoxStyle
     */
    public function strokeWidth($strokeWidth)
    {
        if (is_numeric($strokeWidth)) {
            $this->strokeWidth = (int) $strokeWidth;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }

    /**
     * Sets the x-radius of the corner curvature.
     *
     * @param  int|string $rx
     * @return BoxStyle
     */
    public function rx($rx)
    {
        if (is_numeric($rx)) {
            $this->rx = (int) $rx;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }

    /**
     * Sets the y-radius of the corner curvature.
     *
     * @param  int|string $ry
     * @return BoxStyle
     */
    public function ry($ry)
    {
        if (is_numeric($ry)) {
            $this->ry = (int) $ry;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the attributes for linear gradient fill.
     *
     * @param  Gradient $gradient
     * @return BoxStyle
     */
    public function gradient(Gradient $gradient)
    {
        $this->gradient = $gradient;

        return $this;
    }
}
