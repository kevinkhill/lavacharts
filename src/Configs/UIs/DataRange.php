<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\DataTables\Formats\Format;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

class DataRange extends UI
{
    /**
     * Default options available.
     *
     * @var array
     */
    protected $extDefaults = [
        'format',
        'step',
        'ticks',
        'unitIncrement',
        'blockIncrement',
        'showRangeValues',
        'orientation'
    ];

    /**
     * Builds a new DataRange object.
     *
     * @param \Khill\Lavacharts\Configs\Options $options
     * @param array $config Array of options to set
     */
    public function __construct(Options $options, $config = [])
    {
        parent::__construct($options, $config);
    }

    /**
     * Sets the format for the control.
     *
     * @access public
     * @param  \Khill\Lavacharts\DataTables\Formats\Format
     * @return self
     */
    public function format(Format $format)
    {
        return $this->setOption(__FUNCTION__, $format);
    }

    /**
     * The minimum possible change when dragging the slider thumbs.
     *
     * @access public
     * @param  int|float $step
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function step($step)
    {
        if (is_numeric($step) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        return $this->setOption(__FUNCTION__, $step);
    }

    /**
     * The number of ticks (fixed positions in the range bar) the slider thumbs can occupy.
     *
     * @access public
     * @param  int|float $ticks
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function ticks($ticks)
    {
        if (is_numeric($ticks) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        return $this->setOption(__FUNCTION__, $ticks);
    }

    /**
     * The amount to increment for unit increments of the range extents.
     *
     * A unit increment is equivalent to using the arrow keys to move a slider thumb.
     *
     * @access public
     * @param  int|float $unitIncrement
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function unitIncrement($unitIncrement)
    {
        if (is_numeric($unitIncrement) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        return $this->setOption(__FUNCTION__, $unitIncrement);
    }

    /**
     * The amount to increment for block increments of the range extents.
     *
     * A block increment is equivalent to using the pgUp and pgDown keys to move the slider thumbs.
     *
     * @access public
     * @param  int|float $blockIncrement
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function blockIncrement($blockIncrement)
    {
        if (is_numeric($blockIncrement) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        return $this->setOption(__FUNCTION__, $blockIncrement);
    }

    /**
     * Whether to have labels next to the slider displaying extents of the selected range.
     *
     * @access public
     * @param  string $showRangeValues
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function showRangeValues($showRangeValues)
    {
        if (is_bool($showRangeValues) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this->setOption(__FUNCTION__, $showRangeValues);
    }

    /**
     * The slider orientation. Either 'horizontal' or 'vertical'.
     *
     * @access public
     * @param  string $orientation
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function orientation($orientation)
    {
        $values = [
            'vertical',
            'horizontal'
        ];

        if (Utils::nonEmptyStringInArray($orientation, $values) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                ' which must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->setOption(__FUNCTION__, $orientation);
    }
}
