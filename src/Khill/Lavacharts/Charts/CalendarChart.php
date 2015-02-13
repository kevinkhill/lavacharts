<?php namespace Khill\Lavacharts\Charts;

/**
 * CalendarChart Class
 *
 * Alias for a pie chart with pieHolethat is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v2.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2014, KHill Designs
 * @link       http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link       http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Configs\Stroke;
use Khill\Lavacharts\Configs\TextStyle;

class CalendarChart extends Chart
{
    public $type = 'CalendarChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

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
                'underMonthSpace'
            )
        );
    }

    /**
     * Suplemental function for wrapping all options into the calendar option
     * (Thanks google)
     *
     * @param array $option Array of config options
     */
    private function addCalendarOption($option)
    {
        $this->addOption(array('calendar' => $option));

        return $this;
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
        if (is_int($days)) {
            $this->addCalendarOption(array(__FUNCTION__ => $days));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
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

}
