<?php namespace Lavacharts\Charts;

/**
 * DonutChart Class
 *
 * Alias for a pie chart with pieHolethat is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2014, KHill Designs
 * @link       http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link       http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Lavacharts\Helpers\Helpers;

class DonutChart extends PieChart
{
    public $type = 'DonutChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge(
            $this->defaults,
            array('pieHole')
        );
    }

    /**
     * If between 0 and 1, displays a donut chart. The hole with have a radius
     * equal to $pieHole times the radius of the chart.
     *
     * @param int|float $pieHole
     *
     * @return DonutChart
     */
    public function pieHole($pieHole)
    {
        if (Helpers::between(0.0, $pieHole, 1.0)) {
            $this->addOption(array('pieHole' => $pieHole));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'while, 0 < pieHole < 1 '
            );
        }

        return $this;
    }
}
