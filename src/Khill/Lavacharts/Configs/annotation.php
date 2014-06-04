<?php namespace Khill\Lavacharts\Configs;
/**
 * annotation Properties Object
 *
 * An object containing all the values for the annotation which can
 * be passed into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

class annotation extends configOptions
{
    /**
     * The highContrast state.
     */
    public $highContrast = true;

    /**
     * Text style of the annotation.
     *
     * @var textStyle
     */
    public $textStyle = null;

    /**
     * Builds the annotation object.
     *
     * @param array Associative array containing key => value pairs for the
     * various configuration options.
     * @return \tooltip
     */
    public function __construct($config = array())
    {
        $this->options = array(
            'highContrast',
            'textStyle'
        );

        parent::__construct($config);
    }

    /**
     * For charts that support annotations, the highContrast boolean lets you override Google Charts'
     * choice of the annotation color. By default, highContrast is true, which causes Charts to select
     * an annotation color with good contrast: light colors on dark backgrounds, and dark on light.
     *
     * If you set highContrast to false and don't specify your own annotation color, Google Charts
     * will use the default series color for the annotation
     *
     * @param boolean Annotation color
     * @return \annotation
     */
    public function highContrast($highContrast)
    {
        if(is_bool($highContrast))
        {
            $this->highContrast = $highContrast;
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * An object that specifies the annotation text style.
     *
     * @param textStyle Style of the annotation
     * @return \annotation
     */
    public function textStyle($textStyle)
    {
        if(Helpers::is_textStyle($textStyle))
        {
            $this->textStyle = $textStyle->getValues();
        } else {
            $this->type_error(__FUNCTION__, 'textStyle');
        }

        return $this;
    }

}
