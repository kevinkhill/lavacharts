<?php namespace Khill\Lavacharts\Configs;

/**
 * Color Object
 *
 * Calendar charts use a striped diagonal pattern to indicate that there is no data for a particular day.
 * Use this object with backgroundColor and color options to override the grayscale defaults.
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

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Color extends ConfigObject
{
    /**
     * Foreground color.
     *
     * @var string
     */
    public $color;

    /**
     * Background color.
     *
     * @var string
     */
    public $backgroundColor;

    /**
     * Builds the Color object with specified options
     *
     * @param  array                 $config
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return Color
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Specifies the foreground color.
     *
     * @param  string             $fgColor
     * @throws InvalidConfigValue
     *
     * @return Color
     */
    public function color($fgColor)
    {
        if (is_string($fgColor)) {
            $this->color = $fgColor;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Specifies the background color.
     *
     * @param  string             $bgColor
     * @throws InvalidConfigValue
     *
     * @return Color
     */
    public function backgroundColor($bgColor)
    {
        if (is_string($bgColor)) {
            $this->backgroundColor = $bgColor;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }
}
