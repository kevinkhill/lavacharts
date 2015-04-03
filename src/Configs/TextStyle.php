<?php namespace Khill\Lavacharts\Configs;

/**
 * Text Style Properties Object
 *
 * An object containing all the values for the textStyle which can be
 * passed into the chart's options.
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

class TextStyle extends ConfigObject
{

    /**
     * Text is bold.
     *
     * @var bool
     */
    public $bold;

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
     * Text is italic.
     *
     * @var bool
     */
    public $italic;


    /**
     * Builds the textStyle object when passed an array of configuration options.
     *
     * @param  array                 $config Options for the TextStyle
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return TextStyle
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Set bold on/off for the text element.
     *
     * @param  boolean  $bold
     * @throws InvalidConfigValue
     * @return TextStyle
     */
    public function bold($bold)
    {
        if (is_bool($bold)) {
            $this->bold = $bold;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Set the color for the text element.
     *
     * valid HTML color string, for example: 'red' OR '#004411'
     *
     * @param  string             $color Valid HTML color
     * @throws InvalidConfigValue
     * @return TextStyle
     */
    public function color($color)
    {
        if (is_string($color)) {
            $this->color = $color;
        } else {
            throw $this->invalidConfigValue(
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
     * @param  string             $fontName Valid font name
     * @throws InvalidConfigValue
     * @return TextStyle
     */
    public function fontName($fontName)
    {
        if (is_string($fontName)) {
            $this->fontName = $fontName;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the font size to the textStyle.
     *
     * @param  int                $fontSize Font size in pixels
     * @throws InvalidConfigValue
     * @return TextStyle
     */
    public function fontSize($fontSize)
    {
        if (is_int($fontSize)) {
            $this->fontSize = $fontSize;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Set italic on/off for the text element.
     *
     * @param  boolean  $italic
     * @throws InvalidConfigValue
     * @return TextStyle
     */
    public function italic($italic)
    {
        if (is_bool($italic)) {
            $this->italic = $italic;
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }
}
