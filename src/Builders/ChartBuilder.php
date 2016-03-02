<?php

namespace Khill\Lavacharts\Builders;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Charts\ChartFactory;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Exceptions\InvalidChartType;

/**
 * ChartBuilder Class
 *
 * This class is used to build charts by setting the properties, instead of trying to cover
 * everything in the constructor.
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ChartBuilder extends GenericBuilder
{
    /**
     * Type of chart to create.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Datatable for the chart.
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    protected $datatable = null;

    /**
     * Options for the chart.
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    protected $options = null;

    /**
     * The chart's output override.
     *
     * @var bool
     */
    protected $pngOutput = false;

    /**
     * Set the type of chart to create.
     *
     * @param  string $type Type of chart.
     * @return \Khill\Lavacharts\Charts\ChartBuilder
     * @throws \Khill\Lavacharts\Exceptions\InvalidChartType description
     */
    public function setType($type)
    {
        if (ChartFactory::isValidChart($type) === false) {
            throw new InvalidChartType($type, ChartFactory::getChartTypes());
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Sets the DataTable for the chart.
     *
     * @param \Khill\Lavacharts\DataTables\DataTable $datatable
     * @return \Khill\Lavacharts\Charts\ChartBuilder
     */
    public function setDatatable(DataTable $datatable)
    {
        $this->datatable = $datatable;

        return $this;
    }

    /**
     * Sets the options for the chart.
     *
     * @param array $options
     * @return \Khill\Lavacharts\Charts\ChartBuilder
     */
    public function setOptions($options)
    {
        $this->options = new Options($options);

        return $this;
    }

    /**
     * Sets the charts output override.
     *
     * @param  bool $png
     * @return \Khill\Lavacharts\Charts\ChartBuilder
     */
    public function setPngOutput($png)
    {
        $this->pngOutput = (is_bool($png) ? $png : false);

        return $this;
    }

    /**
     * Creates the chart from the assigned values.
     *
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function getChart()
    {
        $chart =  '\\Khill\\Lavacharts\\Charts\\' . $this->type;

        $lavachart = new $chart(
            $this->label,
            $this->datatable,
            $this->options,
            $this->elementId
        );

        $lavachart->setPngOutput($this->pngOutput);

        return $lavachart;
    }
}
