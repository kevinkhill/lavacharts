<?php namespace Khill\Lavacharts\Charts;

/**
 * DonutChart Class
 *
 * Alias for a pie chart with pieHolethat is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Charts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://www.gnu.org/licenses/gpl.html GPL-V3
 */

use Khill\Lavacharts\Charts\PieChart;

class DonutChart extends PieChart
{
    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults  = array_merge($this->defaults, array('pieHole'));
        $this->chartType = 'PieChart';
    }

    /**
     * If between 0 and 1, displays a donut chart. The hole with have a radius
     * equal to number times the radius of the chart.
     *
     * @param numeric $pieHole
     *
     * @return DonutChart
     */
    public function pieHole($pieHole)
    {
        if (is_numeric($pieHole) && $pieHole > 0 && $pieHole < 1) {
            $this->addOption(array('pieHole' => $pieHole));
        } else {
            $this->type_error(__FUNCTION__, 'numeric', 'while, 0 < pieHole < 1 ');
        }

        return $this;
    }
}
