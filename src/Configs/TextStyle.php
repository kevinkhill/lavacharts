<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * Text Style ConfigObject
 *
 * An object containing all the values for the textStyle which can be
 * passed into the chart's options.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class TextStyle extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'TextStyle';

    /**
     * Default options for TextStyles
     *
     * @var array
     */
    private $defaults = [
        'bold',
        'color',
        'fontName',
        'fontSize',
        'italic'
    ];

    /**
     * Builds the TextStyle object when passed an array of configuration options.
     *
     * @param  array $config Options for the TextStyle
     * @return \Khill\Lavacharts\Configs\TextStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Set bold on/off for the text element.
     *
     * @param  bool $bold
     * @return \Khill\Lavacharts\Configs\TextStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function bold($bold)
    {
        return $this->setBoolOption(__FUNCTION__, $bold);
    }

    /**
     * Set the color for the text element.
     *
     * valid HTML color string, for example: 'red' OR '#004411'
     *
     * @param  string $color Valid HTML color
     * @return \Khill\Lavacharts\Configs\TextStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function color($color)
    {
        return $this->setStringOption(__FUNCTION__, $color);
    }

    /**
     * Sets the font to the textStyle object.
     *
     * Must be a valid font name.
     *
     * @param  string $fontName Valid font name
     * @return \Khill\Lavacharts\Configs\TextStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function fontName($fontName)
    {
        return $this->setStringOption(__FUNCTION__, $fontName);
    }

    /**
     * Sets the font size to the textStyle.
     *
     * @param  integer $fontSize Font size in pixels
     * @return \Khill\Lavacharts\Configs\TextStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function fontSize($fontSize)
    {
        return $this->setIntOption(__FUNCTION__, $fontSize);
    }

    /**
     * Set italic on/off for the text element.
     *
     * @param  boolean  $italic
     * @return \Khill\Lavacharts\Configs\TextStyle
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function italic($italic)
    {
        return $this->setBoolOption(__FUNCTION__, $italic);
    }
}
