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
 * @copyright  (c) 2016, KHill Designs
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
    const OUTPUT_TEMPLATE = 'templates/chart.tmpl.js';

    /**
     * Chart to create javascript from.
     *
     * @var \Khill\Lavacharts\Charts\Chart
     */
    protected $chart;

    /**
     * Event sprintf template
     *
     * @var string
     */
    protected $eventCallbackTempate;

    /**
     * Format sprintf template
     *
     * @var string
     */
    protected $formatTemplate;

    /**
     * Creates a new ChartJsFactory with the javascript template.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to process
     */
    public function __construct(Chart $chart)
    {

        $this->chart = $chart;

        $this->eventCallbackTempate =
            'google.visualization.events.addListener(this.chart, "%s", function (event) {'.PHP_EOL.
                'return lava.event(event, this.chart, %s);'.PHP_EOL.
            '}.bind(this));'.PHP_EOL;

        $this->formatTemplate =
            'this.formats["col%1$s"] = new google.visualization.%2$s(%3$s);'.PHP_EOL.
            'this.formats["col%1$s"].format(this.data, %1$s);'.PHP_EOL;

        parent::__construct(self::OUTPUT_TEMPLATE);
    }

    /**
     * Builds the template variables from the chart.
     *
     * @since  3.0.0
     * @access protected
     * @return string Javascript code block.
     */
    protected function getTemplateVars()
    {
        $chart = $this->chart; // Workaround for no :: on member vars in php5.4

        $vars = [
            'chartLabel'   => $this->chart->getLabelStr(),
            'chartType'    => $chart::TYPE,
            'chartVer'     => $chart::VERSION,
            'chartClass'   => $chart::VIZ_CLASS,
            'chartPackage' => $chart::VIZ_PACKAGE,
            'chartData'    => $chart->getDataTableJson(),
            'chartOptions' => $chart->getOptionsJson(),
            'elemId'       => $this->chart->getElementIdStr(),
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
     * @access protected
     * @return string Javascript code block.
     */
    protected function buildEventCallbacks()
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
     * @access protected
     * @return string Javascript code block.
     */
    protected function buildFormatters()
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
