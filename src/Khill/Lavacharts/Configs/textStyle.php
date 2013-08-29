<?php namespace Khill\Lavacharts\Configs;
/**
 * Text Style Properties Object
 *
 * An object containing all the values for the textStyle which can be
 * passed into the chart's options.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

class textStyle extends configOptions
{
    /**
     * Color of the text.
     *
     * @var string
     */
    public $color;

    /**
     * Font name.
     *
     * @var string
     */
    public $fontName;

    /**
     * Size of font, in pixels.
     *
     * @var int
     */
    public $fontSize;


    /**
     * Builds the textStyle object when passed an array of configuration options.
     *
     * @param array Options for the textStyle
     * @return \tooltip
     */
    public function __construct($config = array())
    {
        $this->options = array(
            'color',
            'fontName',
            'fontSize'
        );

        parent::__construct($config);
    }

    /**
     * Set the color for the text element.
     *
     * valid HTML color string, for example: 'red' OR '#004411'
     *
     * @param string Valid HTML color
     * @return \textStyle
     */
    public function color($color)
    {
        if(is_string($color))
        {
            $this->color = $color;
        } else {
            $this->type_error(__FUNCTION__, 'string', ' of a valid HTML color');
        }

        return $this;
    }

    /**
     * Sets the font to the textStyle object.
     *
     * Must be a valid font name.
     *
     * @param string Valid font name
     * @return \textStyle
     */
    public function fontName($fontName)
    {
        if(is_string($fontName))
        {
            $this->fontName = $fontName;
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Sets the font size to the textStyle.
     *
     * Must be a valid int for size in pixels.
     *
     * @param int Font size in pixels
     * @return \textStyle
     */
    public function fontSize($fontSize)
    {
        if(is_int($fontSize))
        {
            $this->fontSize = $fontSize;
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

}
