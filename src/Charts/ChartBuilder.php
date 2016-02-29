<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
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
class ChartBuilder
{
    /**
     * Type of chart to create.
     *
     * @var string
     */
    private $type = null;

    /**
     * The chart's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    private $label = null;

    /**
     * Datatable for the chart.
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    private $datatable = null;

    /**
     * Options for the chart.
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    private $options = null;

    /**
     * The chart's unique elementId.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    private $elementId = null;

    /**
     * Set the type of chart to create.
     *
     * @param  string $type Type of chart.
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
     * Creates and sets the label for the chart.
     *
     * @param  string|\Khill\Lavacharts\Values\Label $label
     * @return \Khill\Lavacharts\Charts\ChartBuilder
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function setLabel($label)
    {
        $this->label = new Label($label);

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
     * Creates and sets the elementId for the chart.
     *
     * @param  string|\Khill\Lavacharts\Values\ElementId $elementId
     * @return \Khill\Lavacharts\Charts\ChartBuilder
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function setElementId($elementId)
    {
        $this->elementId = new ElementId($elementId);

        return $this;
    }

    /**
     * Creates the chart from the assigned values.
     *
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function getChart()
    {
        $chart = __NAMESPACE__ . '\\' . $this->type;

        $lavachart = new $chart(
            $this->label,
            $this->datatable,
            $this->options,
            $this->elementId
        );

        unset($this->type);
        unset($this->label);
        unset($this->datatable);
        unset($this->options);
        unset($this->elementId);

        return $lavachart;
    }
}
