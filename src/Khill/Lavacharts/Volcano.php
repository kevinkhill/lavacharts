<?php namespace Khill\Lavacharts;

/**
 * Volcano Storage Class
 *
 * Holds all defined charts and datatables.
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
use Khill\Lavacharts\Configs\DataTable;
use Khill\Lavacharts\Exceptions\InvalidLabel;
use Khill\Lavacharts\Exceptions\LabelNotFound;
use Khill\Lavacharts\Exceptions\ChartNotFound;

class Volcano
{
    /**
     * @var array Holds all of the defined Charts.
     */
    private $dataTables = array();

    /**
     * @var array Holds all of the defined Charts.
     */
    private $charts = array();

    /**
     * Stores a datatable in the volcano datastore.
     *
     * @param  Khill\Lavacharts\Config\DataTable $dataTable
     * @param  string $label Identifying label for the datatable.
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function storeDataTable(DataTable $dataTable, $label)
    {
        if (is_string($label)) {
            $this->dataTables[$label] = $dataTable;
        } else {
            throw new InvalidLabel($label);
        }
    }

    /**
     * Retrieves a datatable from the volcano datastore.
     *
     * @param  string $label Identifying label for the datatable.
     * @throws Khill\Lavacharts\Exceptions\LabelNotFound
     * @throws Khill\Lavacharts\Exceptions\InvalidLabel
     *
     * @return Khill\Lavacharts\Configs\DataTable
     */
    public function getDataTable($label)
    {
        if ($this->checkDataTable($label)) {
            return $this->dataTables[$label];
        } else {
            throw new LabelNotFound($label);
        }
    }

    /**
     * Simple true/false test if a datatable exists.
     *
     * @param  string $label Identifying label of a datatable to check.
     * @throws Khill\Lavacharts\Exceptions\InvalidLabel
     *
     * @return bool
     */
    public function checkDataTable($label)
    {
        if (is_string($label)) {
            if (array_key_exists($label, $this->dataTables)) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new InvalidLabel($label);
        }
    }

    /**
     * Stores a chart in the volcano datastore.
     *
     * @param  Khill\Lavacharts\Charts\Chart $chart
     * @param  string $label Identifying label for the chart.
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function storeChart(Chart $chart)
    {
        if (array_key_exists($chart->type, $this->charts) && array_key_exists($chart->label, $this->charts[$chart->type])) {
            //trigger_error("Warning, a chart with the label $chart->label already exists, overwriting", E_USER_ERROR);
            throw new Exception("Error Processing Request", 1);
        }

        $this->charts[$chart->type][$chart->label] = $chart;
    }

    /**
     * Retrieves a chart from the volcano datastore.
     *
     * @param  string $label Identifying label for the chart.
     * @throws Khill\Lavacharts\Exceptions\LabelNotFound
     * @throws Khill\Lavacharts\Exceptions\InvalidLabel
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
     * @throws Khill\Lavacharts\Exceptions\InvalidLabel
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
