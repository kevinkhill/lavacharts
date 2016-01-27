<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Configs\Stroke;
use \Khill\Lavacharts\Configs\TextStyle;
use \Khill\Lavacharts\Configs\Color;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * CalendarChart Class
 *
 * A calendar chart is a visualization used to show activity over the course of a long span of time,
 * such as months or years. They're best used when you want to illustrate how some quantity varies
 * depending on the day of the week, or how it trends over time.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      2.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class CalendarChart extends Chart
{
    /**
     * Common methods
     */
    use \Khill\Lavacharts\Traits\ColorAxisTrait;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'CalendarChart';

    /**
     * Javascript chart version.
     *
     * @var string
     */
    const VERSION = '1.1';

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'calendar';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Calendar';

    /**
     * Default options for the Chart
     *
     * @var array
     */
    private $extChartDefaults = [
        'calendar',
        'noDataPattern'
    ];

    /**
     * Default options for CalendarCharts
     *
     * @var array
     */
    private $calendarDefaults = [
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
        'forceIFrame'
    ];

    /**
     * Builds a new chart with the given label.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable  DataTable used for the chart.
     * @param array                                   $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     * @internal param array $options Array of options to set for the chart.
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $calendarOptions = new Options($this->calendarDefaults);

        $options = new Options($this->extChartDefaults);
        $options->merge($calendarOptions);
        $options->set('calendar', $calendarOptions);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }

    /**
     * Tweaking addOption function for wrapping all options into the calendar config option.
     *
     * @param  string $option Option to set
     * @param  mixed  $value
     * @return \Khill\Lavacharts\Charts\CalendarChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidOption
     */
    protected function setCalendarOption($option, $value)
    {
        $this->options->get('calendar')->set($option, $value);

        return $this;
    }

    /**
     * Overriding getOption function to pull config options from calendar array.
     * (Thanks google)
     *
     * @param  string $option Which option to fetch
     * @return mixed
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __get($option)
    {
        if ($this->options->get('calendar')->hasOption($option)) {
            return $this->options->get('calendar')->get($option);
        } elseif ($this->options->hasOption($option)) {
            return $this->options->get($option);
        } else {
            $calendarOptions = $this->options->get('calendar')->getOptions();
            $nonCalendarOptions = $this->options->getOptions();

            $options = array_merge($calendarOptions, $nonCalendarOptions);

            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($options)
            );
        }
    }

    /**
     * The cellColor option lets you customize the border of the calendar day squares
     *
     * @param  array  $strokeConfig
     * @return \Khill\Lavacharts\Charts\CalendarChart
     */
    public function cellColor($strokeConfig)
    {
        return $this->setCalendarOption(__FUNCTION__, new Stroke($strokeConfig));
    }

    /**
     * Sets the size of the calendar day squares
     *
     * @param  integer $cellSize
     * @return \Khill\Lavacharts\Charts\CalendarChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function cellSize($cellSize)
    {
        if (is_int($cellSize) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'int'
            );
        }

        return $this->setCalendarOption(__FUNCTION__, $cellSize);
    }

    /**
     * Controls the font style of the week labels at the top of the chart.
     *
     * @param  TextStyle     $labelConfig
     * @return \Khill\Lavacharts\Charts\CalendarChart
     */
    public function dayOfWeekLabel($labelConfig)
    {
        $this->setCalendarOption(__FUNCTION__, new TextStyle($labelConfig));
    }

    /**
     * Sets The distance between the right edge of the week labels and
     * the left edge of the chart day squares.
     *
     * @param  integer $space
     * @return \Khill\Lavacharts\Charts\CalendarChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function dayOfWeekRightSpace($space)
    {
        if (is_int($space) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'int'
            );
        }

        return $this->setCalendarOption(__FUNCTION__, $space);
    }

    /**
     * The single-letter labels to use for Sunday through Saturday.
     *
     * @param  string $days
     * @return \Khill\Lavacharts\Charts\CalendarChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function daysOfWeek($days)
    {
        if (is_string($days) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'string'
            );
        }

        return $this->setCalendarOption(__FUNCTION__, $days);
    }

    /**
     * When the user focuses (say, by hovering) over a day square,
     * calendar charts will highlight the square.
     *
     * @param  Stroke $strokeConfig
     * @return \Khill\Lavacharts\Charts\CalendarChart
     */
    public function focusedCellColor($strokeConfig)
    {
        return $this->setCalendarOption(__FUNCTION__, new Stroke($strokeConfig));
    }

    /**
     * Sets the style for the month labels.
     *
     * @param  array $labelConfig
     * @return \Khill\Lavacharts\Charts\CalendarChart
     */
    public function monthLabel($labelConfig)
    {
        return $this->setCalendarOption(__FUNCTION__, new TextStyle($labelConfig));
    }

    /**
     * Months with data values are delineated from others using a border in this style.
     *
     * @param  array $strokeConfig
     * @return \Khill\Lavacharts\Charts\CalendarChart
     */
    public function monthOutlineColor($strokeConfig)
    {
        return $this->setCalendarOption(__FUNCTION__, new Stroke($strokeConfig));
    }

    /**
     * The number of pixels between the bottom of the month labels and
     * the top of the day squares.
     *
     * @param  int $space
     * @return \Khill\Lavacharts\Charts\CalendarChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function underMonthSpace($space)
    {
        if (is_int($space) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->setCalendarOption(__FUNCTION__, $space);
    }

    /**
     * The number of pixels between the bottom-most year label and
     * the bottom of the chart.
     *
     * @param  integer $space
     * @return \Khill\Lavacharts\Charts\CalendarChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function underYearSpace($space)
    {
        if (is_int($space) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->setCalendarOption(__FUNCTION__, $space);
    }

    /**
     * Months without data values are delineated from others using a border in this style.
     *
     * @param  array $strokeConfig
     * @return \Khill\Lavacharts\Charts\CalendarChart
     */
    public function unusedMonthOutlineColor($strokeConfig)
    {
        return $this->setCalendarOption(__FUNCTION__, new Stroke($strokeConfig));
    }

    /**
     * Draws the chart inside an inline frame.
     * Note that on IE8, this option is ignored; all IE8 charts are drawn in i-frames.
     *
     * @param  bool $iframe
     * @return \Khill\Lavacharts\Charts\CalendarChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function forceIFrame($iframe)
    {
        if (is_bool($iframe) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->setCalendarOption(__FUNCTION__, $iframe);
    }

    /**
     * An object that specifies a mapping between color column values and colors or a gradient scale.
     *
     * @param  array $colorConfig
     * @return \Khill\Lavacharts\Charts\CalendarChart
     */
    public function noDataPattern($colorConfig)
    {
        return $this->setOption(__FUNCTION__, new Color($colorConfig));
    }
}
