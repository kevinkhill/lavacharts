<?php namespace Khill\Lavacharts;

/**
 * Volcano Storage Class
 *
 * Storage class that holds all defined charts.
 *
 * @category  Class
 * @package   Khill\Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Exceptions\InvalidLabel;
use Khill\Lavacharts\Exceptions\LabelNotFound;
use Khill\Lavacharts\Exceptions\ChartNotFound;

class Volcano
{
    /**
     * @var array Holds all of the defined Charts.
     */
    private $charts = array();

    /**
     * Stores a chart in the volcano datastore.
     *
     * @param  Khill\Lavacharts\Charts\Chart $chart Chart to store in the volcano.
     * @param  string $label Identifying label for the chart.
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function storeChart(Chart $chart)
    {
        $this->charts[$chart->type][$chart->label] = $chart;

        return true;
    }

    /**
     * Retrieves a chart from the volcano datastore.
     *
     * @param  string $label Identifying label for the chart.
     * @throws Khill\Lavacharts\Exceptions\ChartNotFound
     *
     * @return Khill\Lavacharts\Charts\Chart
     */
    public function getChart($type, $label)
    {
        if ($this->checkChart($type, $label)) {
            return $this->charts[$type][$label];
        } else {
            throw new ChartNotFound($type, $label);
        }
    }

    /**
     * Simple true/false test if a chart exists.
     *
     * @param  string $label Identifying label of a chart to check.
     *
     * @return bool
     */
    public function checkChart($type, $label)
    {
        if (array_key_exists($type, $this->charts)) {
             if (array_key_exists($label, $this->charts[$type])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
