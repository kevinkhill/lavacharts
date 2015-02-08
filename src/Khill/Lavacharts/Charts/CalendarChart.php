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

class CalendarChart extends Chart
{
    public $type = 'CalendarChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge(
            $this->defaults,
            array(
                'cellColor'
            )
        );
    }

    /**
     * The cellColor option lets you customize the border of the calendar day squares
     *
     * @param Stroke $stroke
     *
     * @return CalendarChart
     */
    public function cellColor(Stroke $s)
    {
        $this->addOption($s->toArray(__FUNCTION__));

        return $this;
    }
}
