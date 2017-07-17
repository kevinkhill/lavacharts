<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Javascript\ChartJsFactory;
use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\Contracts\JsFactory;
use Khill\Lavacharts\Support\Contracts\Visualization;
use Khill\Lavacharts\Support\Contracts\Wrappable;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Support\StringValue as Str;
use Khill\Lavacharts\Support\Traits\HasDataTableTrait as HasDataTable;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;

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
class Chart extends Renderable implements Customizable, Javascriptable, JsFactory, Visualization, Wrappable
{
    use HasDataTable, ToJavascript;

    /**
     * Type of wrappable class
     */
    const WRAP_TYPE = 'chartType';

    /**
     * Builds a new chart with the given label.
     *
     * @param string        $label   Identifying label for the chart.
     * @param DataInterface $data    DataTable used for the chart.
     * @param array         $options Options for the chart.
     */
    public function __construct($label, DataInterface $data = null, array $options = [])
    {
        $this->label     = Str::verify($label);
        $this->datatable = $data;

        $this->setOptions($options);

        if ($this->options->hasAndIs('elementId', 'string')) {
            $this->elementId = $this->options->elementId;
        }
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
        return self::GOOGLE_VISUALIZATION . $this->getType();
    }

    /**
     * Get the JsFactory for the chart.
     *
     * @return ChartJsFactory
     */
    public function getJsFactory()
    {
        return new ChartJsFactory($this);
    }

    /**
     * Array representation of the Chart.
     *
     * @return array
     */
    public function toArray()
    {
        // If the DataTable has any set options, they will be merged
        // with the chart options.
        if (method_exists($this->datatable, 'getOptions')) {
            $this->mergeOptions($this->datatable->getOptions());
        }

        $chartArray = [
            'label'     => $this->getLabel(),
            'type'      => $this->getType(),
            'class'     => $this->getJsClass(),
//            'package'   => $this->getJsPackage(),
            'elementId' => $this->getElementId(),
            'options'   => $this->options,
//            'chartVer'     => $this->getVersion(), TODO: check if needed
//            'events'    => $this->getEvents(),
            'formats'   => $this->getFormats(),
//            'datatable' => '',
            'pngOutput' => false,
        ];

        if (method_exists($this->datatable, 'toJsDataTable')) {
            $chartArray['datatable'] = $this->datatable;//toJsDataTable();
        }

        return $chartArray;
    }

    /**
     * Retrieves the events if any have been assigned to the chart.
     *
     *
     * If no events are defined, then an empty buffer will be returned.
     * Valid events will be converted to Javascriptable Event objects.
     *
     * @since  3.2.0
     * @return Buffer The contents will be javascript source
     */
    private function getEvents()
    {
        $buffer = new Buffer();

        if (! $this->options->has('events')) {
            return $buffer;
        }

        foreach ($this->options->events as $event => $callback) {
            $buffer->append(
                new Event($event, $callback)
            );
        }

        return $buffer;
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
     * @since  3.2.0
     * @return Buffer The contents will be javascript source
     */
    private function getFormats()
    {
        $buffer = new Buffer();

        if (! method_exists($this->datatable, 'getFormattedColumns')) {
            $buffer->append('console.log("');
            $buffer->append('[lavacharts] The implementation of DataInterface did not have ');
            $buffer->append('getFormattedColumns() defined, so the data was not formatted.');
            $buffer->append('");');

            return $buffer;
        }

        /**
         * @var \Khill\Lavacharts\DataTables\Columns\Column $column
         */
        foreach ($this->datatable->getFormattedColumns() as $column) {
            $buffer->append(
                $column->getFormat()->toJavascript()
            );
        }

        return $buffer;
    }

    /**
     * Return an array of arguments to pass to the format string provided
     * by getJavascriptFormat().
     *
     * @return array
     */
    public function getJavascriptSource()
    {
        return [
            $this->getJsPackage(),
            $this->toJson(),
            $this->datatable->toJsDataTable(),
        ];
//        return $this->toArray();
    }

    /**
     * Return a format string that will be used to convert the class to javascript.
     *
     * @lang javascript
     * @return string
     */
    public function getJavascriptFormat()
    {
        return '
            window.lava.addPackages("%s");
            
            window.lava.on("google:loaded", function (google) {
                var chart = window.lava.createChartFromJson(%s);
                    
                chart.setData(%s);
                                
                window.lava.store(chart);
                
                chart.render();
            });
        ';
    }
}
