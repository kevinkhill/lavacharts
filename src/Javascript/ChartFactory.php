<?php

namespace Khill\Lavacharts\Javascript;

use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\DataTables\DataTable;

/**
 * ChartFactory Class
 *
 * This class takes Charts and uses all of the info to build the complete
 * javascript blocks for outputting into the page.
 *
 * @category   Class
 * @package    Khill\Lavacharts
 * @subpackage Javascript
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ChartFactory extends JavascriptFactory
{
    /**
     * Chart to create javascript from.
     *
     * @var \Khill\Lavacharts\Charts\Chart
     */
    private $chart;

    /**
     * Creates a new ChartFactory with the javascript template.
     *
     * @access public
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to process
     * @param  \Khill\Lavacharts\Values\ElementId $elementId HTML element id to output into.
     */
    public function __construct(Chart $chart, ElementId $elementId)
    {
        $this->chart        = $chart;
        $this->elementId    = $elementId;
        $this->template     = $this->getTemplate();
        $this->templateVars = $this->getTemplateVars();
    }

    /**
     * Builds the template variables from the chart.
     *
     * @since  3.0.0
     * @access private
     * @return string Javascript code block.
     */
    private function getTemplateVars()
    {
        $chart = $this->chart; // Workaround for no :: on member vars in php5.4

        $vars = [
            'chartLabel'   => (string) $chart->getLabel(),
            'chartType'    => $chart::TYPE,
            'chartVer'     => $chart::VERSION,
            'chartClass'   => $chart::VIZ_CLASS,
            'chartPackage' => $chart::VIZ_PACKAGE,
            'chartData'    => json_encode($chart->getDataTable()),
            'chartOptions' => json_encode($chart),
            'elemId'       => (string) $this->elementId,
            'dataVer'      => DataTable::VERSION,
            'dataClass'    => DataTable::VIZ_CLASS,
            'formats'      => '',
            'events'       => ''
        ];

        if ($chart->getDataTable()->hasFormattedColumns()) {
            $vars['formats'] = $this->buildFormatters();
        }

        if ($this->chart->hasEvents()) {
            $vars['events'] = $this->buildEventCallbacks();
        }

        return $vars;
    }

    /**
     * Builds the javascript object of event callbacks.
     *
     * @access private
     * @return string Javascript code block.
     */
    private function buildEventCallbacks()
    {
        $output = '';
        $events = $this->chart->getEvents();

        foreach ($events as $event => $callback) {
            $output .= sprintf(
                'google.visualization.events.addListener($this.chart, "%1$s", function (event) {'.
                '    return lava.event(event, $this.chart, %2$s);'.
                '});',
                $event,
                $callback
            ).PHP_EOL.PHP_EOL;
        }

        return $output;
    }

    /**
     * Builds the javascript for the datatable column formatters.
     *
     * @access private
     * @return string Javascript code block.
     */
    private function buildFormatters()
    {
        $output = '';
        $columns = $this->chart->getDataTable()->getFormattedColumns();

        foreach ($columns as $index => $column) {
            $format = $column->getFormat();

            $output .= sprintf(
                '$this.formats["col%1$s"] = new google.visualization.%2$s(%3$s);' .
                '$this.formats["col%1$s"].format($this.data, %1$s);',
                $index,
                $format->getType(),
                json_encode($format)
            ).PHP_EOL;
        }

        return $output;
    }

    /**
     * Returns the dashboard javascript template.
     *
     * @since  3.0.0
     * @access private
     * @return string Javascript template
     */
    private function getTemplate()
    {
        return <<<'CHART'
        lava.events.on('jsapi:ready', function (google) {
            /**
             * If the object does not exist for a given chart type, initialize it.
             * This will prevent overriding keys when multiple charts of the same
             * type are being rendered on the same page.
             */
            if ( typeof lava.charts.<chartType> == "undefined" ) {
                lava.charts.<chartType> = {};
            }

            //Creating a new lavachart object
            lava.charts.<chartType>["<chartLabel>"] = new lava.Chart();

            //Checking if output div exists
            if (! document.getElementById("<elemId>")) {
                throw new Error('[Lavacharts] No matching element was found with ID "<elemId>"');
            }

            lava.charts.<chartType>["<chartLabel>"].render = function (data) {
                var $this = lava.charts.<chartType>["<chartLabel>"];

                $this.data = new <dataClass>(<chartData>, <dataVer>);

                $this.options = <chartOptions>;

                $this.chart = new <chartClass>(document.getElementById("<elemId>"));

                <formats>

                <events>

                $this.chart.draw($this.data, $this.options);
            };

            lava.charts.<chartType>["<chartLabel>"].setData = function (data) {
                var $this = lava.charts.<chartType>["<chartLabel>"];

                $this.data = new <dataClass>(data, <dataVer>);
            };

            lava.charts.<chartType>["<chartLabel>"].redraw = function () {
                var $this = lava.charts.<chartType>["<chartLabel>"];

                $this.chart.draw($this.data, $this.options);
            };

            lava.registerChart("<chartType>", "<chartLabel>");

            google.load('visualization', '<chartVer>', {
                packages: ['<chartPackage>'],
                callback: function() {
                    lava.charts.<chartType>["<chartLabel>"].render();
                }
            });
        });
CHART;
    }
}
