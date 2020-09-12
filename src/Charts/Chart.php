<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\Visualization;
use Khill\Lavacharts\Support\Contracts\Wrappable;
use Khill\Lavacharts\Support\Renderable;

/**
 * Class Chart
 *
 * Parent to all charts which has common properties and methods
 * used between all the different charts.
 *
 *
 * @package       Khill\Lavacharts\Charts
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
class Chart extends Renderable implements Visualization, Wrappable
{
    /**
     * Type of wrappable class
     */
    const WRAP_TYPE = 'chartType';

    /**
     * Create a new chart from a named type.
     *
     * The label will be a generated string since it cannot be empty.
     *
     * @param string $chartType
     * @return Chart
     */
    public static function create($chartType)
    {
        $chartType = __NAMESPACE__ . '\\' . $chartType;

        return new $chartType(md5($chartType.'-'.microtime()));
    }

    /**
     * Builds a new chart with the given label.
     *
     * @param string        $label   Identifying label for the chart.
     * @param DataInterface $data    DataTable used for the chart.
     * @param array         $options Options for the chart.
     */
    public function __construct($label, DataInterface $data = null, array $options = [])
    {
        parent::__construct($label, $data, $options);
    }

    /**
     * Wrap the current chart with a ChartWrapper.
     *
     * @since 4.0.0
     * @return ChartWrapper
     */
    public function getChartWrapper()
    {
        $this->isRenderable = false;

        return new ChartWrapper($this->getType(), $this->getElementId());
    }

    /**
     * Returns the Filter wrap type.
     *
     * @since  3.0.5
     * @return string
     */
    public function getWrapType()
    {
        return static::WRAP_TYPE;
    }

    /**
     * Returns the chart version.
     *
     * So far, all the charts but Calendar are version 1
     *
     * @since  3.0.5
     * @return string
     */
    public function getVersion()
    {
        return '1';
    }

    /**
     * Returns the chart visualization class.
     *
     * Most charts are part of the "corechart" package.
     *
     * @since  3.0.5
     * @return string
     */
    public function getJsPackage()
    {
        return 'corechart';
    }

    /**
     * Returns the javascript visualization package for instantiation.
     *
     * The chart type it is automatically prepended with "google.visualization." which
     * is the default for most charts.
     *
     * @since  3.0.5
     * @return string
     */
    public function getJsClass()
    {
        return static::GOOGLE_VISUALIZATION . $this->getType();
    }

    /**
     * Retrieves the formats from the datatable that is defined on the chart.
     *
     *
     * The formats will be serialized down to javascript source and added
     * to a string buffer.
     *
     * If no formats are defined, then an empty buffer will be returned.
     *
     * @since  4.0.0
     * @return array
     */
    public function getFormats()
    {
        $formats = [];

        foreach ($this->datatable->getFormattedColumns() as $column) {
            $formats[] = $column->getFormat()->toArray();
        }

        return $formats;
    }

    /**
     * Array representation of the Chart.
     *
     * @return array
     */
    public function toArray()
    {
        $chartArray = [
            'pngOutput' => false,
            'label'     => $this->getLabel(),
            'type'      => $this->getType(), // TODO: unused in js side?
            'class'     => $this->getJsClass(),
            'elementId' => $this->getElementId(),
            'datatable' => $this->getDataTable(),
            'packages'  => [$this->getJsPackage()],
        ];

        if ($this->hasOptions()) {
            $chartArray['options'] = $this->options->without(['elementId', 'events'])->toArray();

            if ($this->hasOption('events')) {
                $chartArray['events'] = $this->options->get('events');
            }
        }

        if (method_exists($this->datatable, 'hasFormattedColumns')) {
            if ($this->datatable->hasFormattedColumns()) {
                $chartArray['formats'] = $this->getFormats();
            }
        }

        // TODO: needs testing
        if (method_exists($this, 'getPngOutput')) {
            $chartArray['pngOutput'] = $this->getPngOutput();
        }

        // TODO: needs testing
        if (method_exists($this, 'getMaterialOutput')) {
            if ($this->getMaterialOutput()) {
                $chartArray['options'] = sprintf(
                    $this->getJsClass() . '.convertOptions(%s)',
                    $this->getOptions()->toJson()
                );
            }
        }

        return $chartArray;
    }
}
