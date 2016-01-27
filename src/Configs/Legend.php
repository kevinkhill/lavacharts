<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * Legend ConfigObject
 *
 * An object containing all the values for the legend which can be
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
class Legend extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Legend';

    /**
     * Default options for Legend
     *
     * @var array
     */
    private $defaults = [
        'position',
        'alignment',
        'textStyle'
    ];

    /**
     * Builds the legend object when passed an array of configuration options.
     *
     * @param  array $config Options for the legend
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
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
     * @return \Khill\Lavacharts\Configs\Legend
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function position($position)
    {
        $values = [
            'right',
            'top',
            'bottom',
            'in',
            'none'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $position, $values);
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
     * @return \Khill\Lavacharts\Configs\Legend
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function alignment($alignment)
    {
        $values = [
            'start',
            'center',
            'end'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $alignment, $values);
    }

    /**
     * An array that specifies the legend text style options.
     *
     * @param  array $textStyleConfig
     * @return \Khill\Lavacharts\Configs\Legend
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function textStyle($textStyleConfig)
    {
        return $this->setOption(__FUNCTION__, new TextStyle($textStyleConfig));
    }
}
