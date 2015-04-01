<?php namespace Khill\Lavacharts\Charts;

/**
 * CalendarChart Class
 *
 * A calendar chart is a visualization used to show activity over the course of a long span of time,
 * such as months or years. They're best used when you want to illustrate how some quantity varies
 * depending on the day of the week, or how it trends over time.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v2.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Configs\Stroke;
use Khill\Lavacharts\Configs\TextStyle;
use Khill\Lavacharts\Configs\ColorAxis;
use Khill\Lavacharts\Configs\Color;

class CalendarChart extends Chart
{
    public $type = 'CalendarChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->options = array('calendar' => array());

        $this->defaults = array_merge(
            $this->defaults,
            array(
                'cellColor',
                'cellSize',
                'dayOfWeekLabel',
                'dayOfWeekRightSpace',
                'daysOfWeek',
                'focusedCellColor',
                'monthLabel',
                'monthOutlineColor',
                'underMonthSpace',
                'underYearSpace',
                'unusedMonthOutlineColor',
                'colorAxis',
                'forceIFrame',
                'noDataPattern'
            )
        );
    }

    /**
     * Tweaking addOption function for wrapping all options into the calendar config option.
     *
     * @param array $option Array of config options
     */
    protected function addCalendarOption($o)
    {
        if (is_array($o)) {
            $this->options['calendar'] = array_merge($this->options['calendar'], $o);
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array'
            );
        }

        return $this;
    }

    /**
     * Overriding getOption function to pull config options from calendar array.
     * (Thanks google)
     *
     * @param  string             $o Which option to fetch
     * @throws InvalidConfigValue
     * @return mixed
     */
    public function getOption($o)
    {
        if (is_string($o) && array_key_exists($o, $this->options['calendar'])) {
            return $this->options['calendar'][$o];
        } else if (is_string($o) && array_key_exists($o, $this->options)) {
            return $this->options[$o];
        } else {
            $calendarOptions = $this->options['calendar'];
            $nonCalendarOptions = array_diff($this->options, $calendarOptions);
            $options = array_merge($calendarOptions, $nonCalendarOptions);

            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString(array_keys($options))
            );
        }
    }

    /**
     * The cellColor option lets you customize the border of the calendar day squares
     *
     * @param Stroke $stroke
     *
     * @return CalendarChart
     */
    public function cellColor(Stroke $stroke)
    {
        $this->addCalendarOption($stroke->toArray(__FUNCTION__));

        return $this;
    }

    /**
     * Sets the size of the calendar day squares
     *
     * @param int $cellSize
     *
     * @return CalendarChart
     */
    public function cellSize($cellSize)
    {
        if (is_int($cellSize)) {
            $this->addCalendarOption(array(__FUNCTION__ => $cellSize));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Controls the font style of the week labels at the top of the chart.
     *
     * @param  TextStyle $label
     *
     * @return CalendarChart
     */
    public function dayOfWeekLabel(TextStyle $label)
    {
        $this->addCalendarOption($label->toArray(__FUNCTION__));
    }

    /**
     * Sets The distance between the right edge of the week labels and
     * the left edge of the chart day squares.
     *
     * @param  int $space
     *
     * @return CalendarChart
     */
    public function dayOfWeekRightSpace($space)
    {
        if (is_int($space)) {
            $this->addCalendarOption(array(__FUNCTION__ => $space));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * The single-letter labels to use for Sunday through Saturday.
     *
     * @param  string $days
     *
     * @return CalendarChart
     */
    public function daysOfWeek($days)
    {
        if (is_string($days)) {
            $this->addCalendarOption(array(__FUNCTION__ => $days));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * When the user focuses (say, by hovering) over a day square,
     * calendar charts will highlight the square.
     *
     * @param Stroke $stroke
     *
     * @return CalendarChart
     */
    public function focusedCellColor(Stroke $stroke)
    {
        $this->addCalendarOption($stroke->toArray(__FUNCTION__));

        return $this;
    }

    /**
     * Sets the style for the month labels.
     *
     * @param  TextStyle $label
     *
     * @return CalendarChart
     */
    public function monthLabel(TextStyle $label)
    {
        $this->addCalendarOption($label->toArray(__FUNCTION__));
    }

    /**
     * Months with data values are delineated from others using a border in this style.
     *
     * @param Stroke $stroke
     *
     * @return CalendarChart
     */
    public function monthOutlineColor(Stroke $stroke)
    {
        $this->addCalendarOption($stroke->toArray(__FUNCTION__));

        return $this;
    }

    /**
     * The number of pixels between the bottom of the month labels and
     * the top of the day squares.
     *
     * @param int $space
     *
     * @return CalendarChart
     */
    public function underMonthSpace($space)
    {
        if (is_int($space)) {
            $this->addCalendarOption(array(__FUNCTION__ => $space));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * The number of pixels between the bottom-most year label and
     * the bottom of the chart.
     *
     * @param int $space
     *
     * @return CalendarChart
     */
    public function underYearSpace($space)
    {
        if (is_int($space)) {
            $this->addCalendarOption(array(__FUNCTION__ => $space));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Months without data values are delineated from others using a border in this style.
     *
     * @param Stroke $stroke
     *
     * @return CalendarChart
     */
    public function unusedMonthOutlineColor(Stroke $stroke)
    {
        $this->addCalendarOption($stroke->toArray(__FUNCTION__));

        return $this;
    }

    /**
     * An object that specifies a mapping between color column values and colors or a gradient scale.
     *
     * @param ColorAxis $colorAxis
     *
     * @return CalendarChart
     */
    public function colorAxis(ColorAxis $colorAxis)
    {
        $this->addOption($colorAxis->toArray(__FUNCTION__));

        return $this;
    }

    /**
     * Draws the chart inside an inline frame.
     * Note that on IE8, this option is ignored; all IE8 charts are drawn in i-frames.
     *
     * @param bool $iframe
     *
     * @return CalendarChart
     */
    public function forceIFrame($iframe)
    {
        if (is_bool($iframe)) {
            $this->addCalendarOption(array(__FUNCTION__ => $iframe));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }
    }

    /**
     * An object that specifies a mapping between color column values and colors or a gradient scale.
     *
     * @param Color $color
     *
     * @return CalendarChart
     */
    public function noDataPattern(Color $color)
    {
        $this->addOption(array(__FUNCTION__ => $color->getValues()));

        return $this;
    }
}
