<?php namespace Khill\Lavacharts\Configs;

/**
 * Stroke Object
 *
 * An object that specifies a the color, thickness and opacity of borders in charts.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @since      v2.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Stroke extends ConfigObject
{
    /**
     * Color to assign the stroke.
     *
     * @var string
     */
    public $stroke;

    /**
     * Opacity of the stroke.
     *
     * @var float
     */
    public $strokeOpacity;

    /**
     * Width of the stroke, in pixels.
     *
     * @var int
     */
    public $strokeWidth;

    /**
     * Builds the Stroke object with specified options
     *
     * @param  array                 $config
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return Stroke
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Sets the color of the stroke.
     *
     * @param  string            $stroke A valid html color string
     * @throws InvalidConfigValue
     * @return Stroke
     */
    public function stroke($s)
    {
        if (is_string($s)) {
            $this->stroke = $s;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the opacity of the stroke.
     *
     * @param  float             $strokeOpacity
     * @throws InvalidConfigValue
     * @return Stroke
     */
    public function strokeOpacity($so)
    {
        if (Utils::between(0.0, $so, 1.0, true)) {
            $this->strokeOpacity = (float) $so;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'float'
            );
        }

        return $this;
    }

    /**
     * Sets the width of the stroke.
     *
     * @param  int                $sw
     * @throws InvalidConfigValue
     * @return Stroke
     */
    public function strokeWidth($sw)
    {
        if (is_int($sw)) {
            $this->strokeWidth = $sw;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }
}
