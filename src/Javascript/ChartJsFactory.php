<?php

namespace Khill\Lavacharts\Javascript;

use \Khill\Lavacharts\Lavacharts;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Values\ElementId;

/**
 * ChartFactory Class
 *
 * This class takes Charts and uses all of the info to build the complete
 * javascript blocks for outputting into the page.
 *
 * @category   Class
 * @package    Khill\Lavacharts\Javascript
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
     * Event format template
     *
     * @var string
     */
    protected $eventTemplate;

    /**
     * Format format template
     *
     * @var string
     */
    protected $formatTemplate;

    /**
     * Element Id to render into
     *
     * @var string
     */
    private $elementId;

    /**
     * Creates a new ChartJsFactory with the javascript template.
     *
     * @param  \Khill\Lavacharts\Charts\Chart    $chart Chart to process
     * @param \Khill\Lavacharts\Values\ElementId $elementId
     */
    public function __construct(Chart $chart, ElementId $elementId)
    {
        $this->chart = $chart;
        $this->elementId = $elementId;

        $this->eventTemplate =
            'google.visualization.events.addListener(this.chart, "%s", function (event) {'.PHP_EOL.
                'return lava.event(event, this.chart, %s);'.PHP_EOL.
            '}.bind(this));'.PHP_EOL;

        $this->formatTemplate =
            'this.formats["col%1$s"] = new %2$s(%3$s);'.PHP_EOL.
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
        /**
         * Patching in 3.0 style template vars
         */
        if (version_compare(Lavacharts::VERSION, '3.1.0', '<')) {
            $vars = $this->getTemplateVars3_0();
        } else {
            $vars = [
                'chartLabel'   => $this->chart->getLabelStr(),
                'chartType'    => $this->chart->getType(),
                'chartVer'     => $this->chart->getVersion(),
                'chartClass'   => $this->chart->getJsClass(),
                'chartPackage' => $this->chart->getJsPackage(),
                'chartData'    => $this->chart->getDataTableJson(),
                'chartOptions' => $this->chart->toJson(),
                'elemId'       => $this->chart->getElementIdStr(),
                'pngOutput'    => false,
                'formats'      => '',
                'events'       => ''
            ];
        }

        if (method_exists($this->chart, 'getPngOutput')) {
            $vars['pngOutput'] = $this->chart->getPngOutput();
        }

        if ($this->chart->getDataTable()->hasFormattedColumns()) {
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
        $buffer = '';
        $events = $this->chart->getEvents();

        foreach ($events as $event => $callback) {
            $buffer .= sprintf(
                $this->eventTemplate,
                $event,
                $callback
            ).PHP_EOL.PHP_EOL;
        }

        return $buffer;
    }

    /**
     * Builds the javascript for the datatable column formatters.
     *
     * @access protected
     * @return string Javascript code block.
     */
    protected function buildFormatters()
    {
        $buffer  = '';
        $columns = $this->chart->getDataTable()->getFormattedColumns();

        /**
         * @var int|string $index
         * @var \Khill\Lavacharts\DataTables\Columns\Column $column
         */
        foreach ($columns as $index => $column) {
            $format = $column->getFormat();

            $buffer .= sprintf(
                $this->formatTemplate,
                $index,
                $format->getJsClass(),
                $format->toJson()
            ).PHP_EOL;
        }

        return $buffer;
    }

    /**
     * Patching the template vars from the updated 3.1 style to 3.0
     *
     * @return array
     */
    private function getTemplateVars3_0()
    {
        $chart = $this->chart;

        return [
            'chartLabel'   => (string) $chart->getLabel(),
            'chartType'    => $chart::TYPE,
            'chartVer'     => $chart::VERSION,
            'chartClass'   => $chart::VIZ_CLASS,
            'chartPackage' => $chart::VIZ_PACKAGE,
            'chartData'    => $chart->getDataTableJson(),
            'chartOptions' => json_encode($chart),
            'elemId'       => $this->elementId,
            'pngOutput'    => false,
            'formats'      => '',
            'events'       => ''
        ];
    }
}
