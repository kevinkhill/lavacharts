<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

class NumberRangeUI extends UI
{
    /**
     * Type of UI config object
     *
     * @var string
     */
    const TYPE = 'NumberRangeUI';

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

    public function __construct($config=[])
    {
        $this->options = new Options($this->defaults);
        $this->options->extend($this->extDefaults);

        parent::__construct($config);
    }

    /**
     * Sets the column formatter.
     *
     * @access public
     * @param  \Khill\Lavacharts\DataTables\Formats\Format
     * @return self
     */
    public function format(Format $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * The minimum possible change when dragging the slider thumbs.
     *
     *
     */
    public function step($step)
    {
        if (is_numeric($step) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        $this->setOption(__FUNCTION__, $step);
    }

    /**
     * The number of ticks (fixed positions in the range bar) the slider thumbs can occupy.
     *
     *
     */
    public function ticks($ticks)
    {
        if (is_numeric($ticks) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        $this->setOption(__FUNCTION__, $ticks);
    }

    /**
     * The amount to increment for unit increments of the range extents.
     *
     * A unit increment is equivalent to using the arrow keys to move a slider thumb.
     *
     *
     */
    public function unitIncrement($unitIncrement)
    {
        if (is_numeric($unitIncrement) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        $this->setOption(__FUNCTION__, $unitIncrement);
    }

    /**
     * The amount to increment for block increments of the range extents.
     *
     * A block increment is equivalent to using the pgUp and pgDown keys to move the slider thumbs.
     *
     *
     */
    public function blockIncrement($blockIncrement)
    {
        if (is_numeric($blockIncrement) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int|float'
            );
        }

        $this->setOption(__FUNCTION__, $blockIncrement);
    }

    /**
     * Whether to have labels next to the slider displaying extents of the selected range.
     *
     *
     */
    public function showRangeValues($showRangeValues)
    {
        if (is_bool($showRangeValues) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        $this->setOption(__FUNCTION__, $showRangeValues);
    }

    /**
     * The slider orientation. Either 'horizontal' or 'vertical'.
     *
     *
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
