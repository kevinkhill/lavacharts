<?php namespace Khill\Lavacharts\Configs;

/**
 * Annotation Properties Object
 *
 * An object containing all the values for the annotation which can
 * be passed into the chart's options.
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

class Annotation extends ConfigObject
{
    /**
     * The highContrast state.
     *
     * @var bool
     */
    public $highContrast = true;

    /**
     * Style of the annotation.
     *
     * @var TextStyle
     */
    public $textStyle;

    /**
     * Builds the Annotation object.
     *
     * @param  array Associative array containing key => value pairs for the various configuration options.
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return Annotation
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * For charts that support annotations, the highContrast bool lets you override Google Charts'
     * choice of the annotation color. By default, highContrast is true, which causes Charts to select
     * an annotation color with good contrast: light colors on dark backgrounds, and dark on light.
     *
     * If you set highContrast to false and don't specify your own annotation color, Google Charts
     * will use the default series color for the annotation
     *
     * @param  bool Annotation color
     * @return Annotation
     */
    public function highContrast($highContrast)
    {
        if (is_bool($highContrast)) {
            $this->highContrast = $highContrast;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * An object that specifies the annotation text style.
     *
     * @param  TextStyle  $textStyle Style of the annotation
     * @return Annotation
     */
    public function textStyle(TextStyle $textStyle)
    {
        $this->textStyle = $textStyle;

        return $this;
    }
}
