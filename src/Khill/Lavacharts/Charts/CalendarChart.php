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
                'dayOfWeekLabel'
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

}
