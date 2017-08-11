<?php

namespace Khill\Lavacharts\Charts;

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
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
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
     *
     * The label will be a generated string since it cannot be empty.
     *
     * @param string $chartType
     */
    public static function create($chartType)
    {
        $chartType = __NAMESPACE__ . '\\' . $chartType;

        return new $chartType(md5(microtime()));
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
     * Convert the Chart to Javascript.
     *
     * @return string
     */
    public function toJavascript()
    {
        return sprintf(
            $this->getJavascriptFormat(),
            $this->getJavascriptSource()
        );
    }

    /**
     * Return the JSON payload that will be passed to lava.createChart.
     *
     * @return string
     */
    public function getJavascriptSource()
    {
        return $this->toJson();
    }

    /**
     * Return a format string that will be used to convert the class to javascript.
     *
     * @lang javascript
     * @return string
     */
    public function getJavascriptFormat()
    {
        return 'window.lava.addNewChart(%s);';
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
            'formats'   => $this->getFormats(),
            'datatable' => $this->getDataTable(),
            'packages'  => [$this->getJsPackage()],
            'options'   => $this->options->without(['elementId', 'events']),
            'events'    => $this->hasOption('events') ? $this->options->events : [],
        ];

        // TODO: needs testing
        if (method_exists($this, 'getPngOutput')) {
            $chartArray['pngOutput'] = $this->getPngOutput();
        }

        // TODO: needs testing
        if (method_exists($this, 'getMaterialOutput') &&
            $this->getMaterialOutput()
        ) {
            $chartArray['options'] = sprintf(
                $this->getJsClass() . '.convertOptions(%s)',
                $this->getOptions()->toJson()
            );
        }

        return $chartArray;
    }
}
