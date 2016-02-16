<?php

namespace Khill\Lavacharts\Javascript;

use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Values\ElementId;
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
class ChartJsFactory extends JavascriptFactory
{
    /**
     * Location of the output template.
     *
     * @var string
     */
    const OUTPUT_TEMPLATE = '/../../javascript/chart.tmpl.js';

    /**
     * Chart to create javascript from.
     *
     * @var \Khill\Lavacharts\Charts\Chart
     */
    private $chart;

    /**
     * Creates a new ChartFactory with the javascript template.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to process
     */
    public function __construct(Chart $chart)
    {
        $this->chart        = $chart;
        $this->template     = file_get_contents(realpath(__DIR__ . self::OUTPUT_TEMPLATE));
        $this->templateVars = $this->getTemplateVars();

        $this->eventCallbackTempate =
            'google.visualization.events.addListener(this.chart, "%1$s", function (event) {'.
            '    return lava.event(event, this.chart, %2$s);'.
            '});';

        $this->formatTemplate =
            'this.formats["col%1$s"] = new google.visualization.%2$s(%3$s);' .
            'this.formats["col%1$s"].format(this.data, %1$s);';
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
            'chartLabel'   => (string) $this->chart->getLabel(),
            'chartType'    => $chart::TYPE,
            'chartVer'     => $chart::VERSION,
            'chartClass'   => $chart::VIZ_CLASS,
            'chartPackage' => $chart::VIZ_PACKAGE,
            'chartData'    => json_encode($chart->getDataTable()),
            'chartOptions' => json_encode($chart->getOptions()),
            'elemId'       => (string) $this->chart->getElementId(),
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
                $this->eventCallbackTempate,
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
        $output  = '';
        $columns = $this->chart->getDataTable()->getFormattedColumns();

        foreach ($columns as $index => $column) {
            $format = $column->getFormat();

            $output .= sprintf(
                $this->formatTemplate,
                $index,
                $format->getType(),
                json_encode($format)
            ).PHP_EOL;
        }

        return $output;
    }
}
