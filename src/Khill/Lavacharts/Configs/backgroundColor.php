<?php namespace Khill\Lavacharts\Configs;
/**
 * backgroundColor Object
 *
 * An object containing all the values for the backgroundColor object which can
 * be passed into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

class backgroundColor extends configOptions
{
    /**
     * The color of the chart border, as an HTML color string.
     *
     * @var string Valid HTML color.
     */
    public $stroke = NULL;

    /**
     * The border width, in pixels.
     *
     * @var int Width in number of pixels.
     */
    public $strokeWidth = NULL;

    /**
     * The chart fill color, as an HTML color string.
     *
     * @var string Valid HTML color.
     */
    public $fill = NULL;


    /**
     * Builds the backgroundColor object with specified options
     *
     * Pass an associative array with values for the keys
     * [ stroke | strokeWidth | fill ]
     *
     * @param array Configuration options
     * @return \backgroundColor
     */
    public function __construct($config = array()) {

        $this->options = array(
            'stroke',
            'strokeWidth',
            'fill'
        );

        parent::__construct($config);
    }

    /**
     * Sets the chart border color. Example: 'red' or '#A2A2A2'
     *
     * @param string Valid HTML color string.
     * @return \backgroundColor
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
     * Sets the chart border width.
     *
     * @param int Border width, in pixels.
     * @return \backgroundColor
     */
    public function strokeWidth($strokeWidth)
    {
        if(is_int($strokeWidth))
        {
            $this->strokeWidth = $strokeWidth;
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * Sets the chart color fill, Example: 'blue' or '#C5C5C5'
     *
     * @param string Valid HTML color string.
     * @return \backgroundColor
     */
    public function fill($fill)
    {
        if(is_string($fill))
        {
            $this->fill = $fill;
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

}
