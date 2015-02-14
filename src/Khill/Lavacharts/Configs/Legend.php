<?php namespace Khill\Lavacharts\Configs;

/**
 * Legend Properties Object
 *
 * An object containing all the values for the legend which can be
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

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class Legend extends ConfigObject
{
    /**
     * Position of the legend.
     *
     * @var string
     */
    public $position;

    /**
     * Alignment of the legend.
     *
     * @var string
     */
    public $alignment;

    /**
     * Text style of the legend.
     *
     * @var TextStyle
     */
    public $textStyle;


    /**
     * Builds the legend object when passed an array of configuration options.
     *
     * @param  array                 $config Options for the legend
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return Legend
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Sets the position of the legend.
     *
     * Can be one of the following:
     * 'right'  - To the right of the chart. Incompatible with the vAxes option.
     * 'top'    - Above the chart.
     * 'bottom' - Below the chart.
     * 'in'     - Inside the chart, by the top left corner.
     * 'none'   - No legend is displayed.
     *
     * @param  string $position Location of legend.
     * @return Legend
     */
    public function position($position)
    {
        $values = array(
            'right',
            'top',
            'bottom',
            'in',
            'none'
        );

        if (is_string($position) && in_array($position, $values)) {
            $this->position = $position;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * Sets the alignment of the legend.
     *
     * Can be one of the following:
     * 'start'  - Aligned to the start of the area allocated for the legend.
     * 'center' - Centered in the area allocated for the legend.
     * 'end'    - Aligned to the end of the area allocated for the legend.
     *
     * Start, center, and end are relative to the style -- vertical or horizontal -- of the legend.
     * For example, in a 'right' legend, 'start' and 'end' are at the top and bottom, respectively;
     * for a 'top' legend, 'start' and 'end' would be at the left and right of the area, respectively.
     *
     * The default value depends on the legend's position. For 'bottom' legends,
     * the default is 'center'; other legends default to 'start'.
     *
     * @param  string $alignment Alignment of the legend.
     * @return Legend
     */
    public function alignment($alignment)
    {
        $values = array(
            'start',
            'center',
            'end'
        );

        if (is_string($alignment) && in_array($alignment, $values)) {
            $this->alignment = $alignment;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * An object that specifies the legend text style.
     *
     * @param  TextStyle $textStyle Style of the legend
     * @return Legend
     */
    public function textStyle(TextStyle $textStyle)
    {
        $this->textStyle = $textStyle->getValues();

        return $this;
    }
}
