<?php namespace Khill\Lavacharts\Configs;

/**
 * Text Style Properties Object
 *
 * An object containing all the values for the textStyle which can be
 * passed into the chart's options.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Configs
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class TextStyle extends ConfigOptions
{
    /**
     * @var string Color of the text.
     */
    public $color = null;

    /**
     * @var string Font name.
     */
    public $fontName = null;

    /**
     * @var int Size of font, in pixels.
     */
    public $fontSize = null;


    /**
     * Builds the textStyle object when passed an array of configuration options.
     *
     * @param  array Options for the TextStyle
     *
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     *
     * @return Khill\Lavacharts\Configs\TextStyle
     */
    public function __construct($config = array())
    {
        $this->className = str_replace("Khill\\Lavacharts\\Configs\\", '', __CLASS__);
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
     *
     * @return Khill\Lavacharts\Configs\TextStyle
     */
    public function color($color)
    {
        if (is_string($color)) {
            $this->color = $color;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'representing a valid HTML color'
            );
        }

        return $this;
    }

    /**
     * Sets the font to the textStyle object.
     *
     * Must be a valid font name.
     *
     * @param string Valid font name
     *
     * @return Khill\Lavacharts\Configs\TextStyle
     */
    public function fontName($fontName)
    {
        if (is_string($fontName)) {
            $this->fontName = $fontName;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the font size to the textStyle.
     *
     * Must be a valid int for size in pixels.
     *
     * @param int Font size in pixels
     *
     * @return Khill\Lavacharts\Configs\TextStyle
     */
    public function fontSize($fontSize)
    {
        if (is_int($fontSize)) {
            $this->fontSize = $fontSize;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }
}
