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
 * @license   http://opensource.org/licenses/GPL-3.0 GPLv3
 */

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Configs\DataTable;
use Khill\Lavacharts\Exceptions\InvalidLabel;
use Khill\Lavacharts\Exceptions\LabelNotFound;

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
    public function storeChart(Chart $chart, $label)
    {
        if (is_string($label)) {
            $this->charts[$label] = $chart;
        } else {
            throw new InvalidLabel($label);
        }
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
    public function getChart($label)
    {
        if ($this->checkChart($label)) {
            return $this->charts[$label];
        } else {
            throw new LabelNotFound($label);
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
    public function checkChart($label)
    {
        if (is_string($label)) {
            if (array_key_exists($label, $this->charts)) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new InvalidLabel($label);
        }
    }
}
