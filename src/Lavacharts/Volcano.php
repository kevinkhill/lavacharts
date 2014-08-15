<?php namespace Lavacharts;

/**
 * Volcano Storage Class
 *
 * Storage class that holds all defined charts.
 *
 * @category  Class
 * @package   Lavacharts
 * @since     v2.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      http://github.com/kevinkhill/Lavacharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/Lavacharts  GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Lavacharts\Charts\Chart;
use Lavacharts\Exceptions\InvalidLabel;
use Lavacharts\Exceptions\ChartNotFound;

class Volcano
{
    /**
     * @var array Holds all of the defined Charts.
     */
    private $charts = array();

    /**
     * Stores a chart in the volcano datastore.
     *
     * @param Lavacharts\Charts\Chart $chart Chart to store in the volcano.
     * @param string                        $label Identifying label for the chart.
     *
     * @throws Lavacharts\Exceptions\InvalidLabel
     */
    public function storeChart(Chart $chart)
    {
        $this->charts[$chart->type][$chart->label] = $chart;

        return true;
    }

    /**
     * Retrieves a chart from the volcano datastore.
     *
     * @param  string                                    $label Identifying label for the chart.
     * @throws Lavacharts\Exceptions\ChartNotFound
     *
     * @return Lavacharts\Charts\Chart
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
     * @param string $label Identifying label of a chart to check.
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
