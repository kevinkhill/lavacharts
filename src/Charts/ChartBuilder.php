<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Exceptions\InvalidChartType;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\StringValue as Str;

/**
 * Class ChartBuilder
 *
 * This class is used to build charts by setting the properties, instead of trying to cover
 * everything in the constructor.
 *
 * @package    Khill\Lavacharts\Builders
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  2020 Kevin Hill
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class ChartBuilder
{
    /**
     * The chart's unique label.
     *
     * @var string
     */
    protected $label = null;

    /**
     * The chart's unique elementId.
     *
     * @var string
     */
    protected $elementId = null;

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
     * @var array
     */
    protected $options = [];

    /**
     * The chart's png output override.
     *
     * @var bool
     */
    protected $pngOutput = false;

    /**
     * The chart's material output override.
     *
     * @var bool
     */
    protected $materialOutput = false;

    /**
     * Set the type of chart to create.
     *
     * @param  string $type Type of chart.
     * @return self
     * @throws \Khill\Lavacharts\Exceptions\InvalidChartType description
     */
    public function setType($type)
    {
        if (ChartFactory::isValidChart($type) === false) {
            throw new InvalidChartType($type);
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Sets the DataTable for the chart.
     *
     * @param DataInterface|null $data
     * @return ChartBuilder
     */
    public function setDatatable(DataInterface $data = null)
    {
        $this->datatable = $data;

        return $this;
    }

    /**
     * Creates and sets the label for the chart.
     *
     * @param  string $label
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = Str::verify($label);

        return $this;
    }

    /**
     * Creates and sets the elementId for the chart.
     *
     * @param  string $elementId
     * @return self
     */
    public function setElementId($elementId)
    {
        $this->elementId = Str::verify($elementId);

        return $this;
    }

    /**
     * Sets the options for the chart.
     *
     * @param  array $options
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Sets the charts output override.
     *
     * @param  bool $png
     * @return self
     */
    public function setPngOutput($png)
    {
        $this->pngOutput = (is_bool($png) ? $png : false);

        return $this;
    }

    /**
     * Sets the charts output override.
     *
     * @param  bool $material
     * @return self
     */
    public function setMaterialOutput($material)
    {
        $this->materialOutput = (is_bool($material) ? $material : false);

        return $this;
    }

    /**
     * Creates the chart from the assigned values.
     *
     * @return Chart
     */
    public function getChart()
    {
        $chartClass =  '\\Khill\\Lavacharts\\Charts\\' . $this->type;

        /** @var Chart $chart */
        $chart = new $chartClass(
            $this->label,
            $this->datatable,
            $this->options
        );

        if (array_key_exists('elementId', $this->options)) {
            $chart->setElementId($this->options['elementId']);
        }

        if (isset($this->elementId)) {
            $chart->setElementId($this->elementId);
        }

        if (method_exists($chart, 'setPngOutput')) {
            $chart->setPngOutput($this->pngOutput);
        }

        if (method_exists($chart, 'setMaterialOutput')) {
            $chart->setMaterialOutput($this->materialOutput);
        }

        return $chart;
    }
}
