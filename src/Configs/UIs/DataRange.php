<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * DataRangeUI Object
 *
 * Parent to the NumberRange and DateRange UI objects.
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs\UIs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class DataRange extends UI
{
    /**
     * Default options available.
     *
     * @var array
     */
    protected $dataRangeDefaults = [
        'format',
        'step',
        'ticks',
        'unitIncrement',
        'blockIncrement',
        'showRangeValues',
        'orientation'
    ];

    /**
     * Builds a new Date or Number RangeUI object.
     *
     * @param array $config Array of options to set
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->dataRangeDefaults);

        parent::__construct($options, $config);
    }

    /**
     * The minimum possible change when dragging the slider thumbs.
     *
     * @access public
     * @param  int|float $step
     * @return \Khill\Lavacharts\Configs\UIs\DataRange
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function step($step)
    {
        return $this->setNumericOption(__FUNCTION__, $step);
    }

    /**
     * The number of ticks (fixed positions in the range bar) the slider thumbs can occupy.
     *
     * @access public
     * @param  int|float $ticks
     * @return \Khill\Lavacharts\Configs\UIs\DataRange
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function ticks($ticks)
    {
        return $this->setNumericOption(__FUNCTION__, $ticks);
    }

    /**
     * The amount to increment for unit increments of the range extents.
     *
     * A unit increment is equivalent to using the arrow keys to move a slider thumb.
     *
     * @access public
     * @param  int|float $unitIncrement
     * @return \Khill\Lavacharts\Configs\UIs\DataRange
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function unitIncrement($unitIncrement)
    {
        return $this->setNumericOption(__FUNCTION__, $unitIncrement);
    }

    /**
     * The amount to increment for block increments of the range extents.
     *
     * A block increment is equivalent to using the pgUp and pgDown keys to move the slider thumbs.
     *
     * @access public
     * @param  int|float $blockIncrement
     * @return \Khill\Lavacharts\Configs\UIs\DataRange
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function blockIncrement($blockIncrement)
    {
        return $this->setNumericOption(__FUNCTION__, $blockIncrement);
    }

    /**
     * Whether to have labels next to the slider displaying extents of the selected range.
     *
     * @access public
     * @param  string $showRangeValues
     * @return \Khill\Lavacharts\Configs\UIs\DataRange
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function showRangeValues($showRangeValues)
    {
        return $this->setBoolOption(__FUNCTION__, $showRangeValues);
    }

    /**
     * The slider orientation. Either 'horizontal' or 'vertical'.
     *
     * @access public
     * @param  string $orientation
     * @return \Khill\Lavacharts\Configs\UIs\DataRange
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function orientation($orientation)
    {
        $values = [
            'vertical',
            'horizontal'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $orientation, $values);
    }
}
