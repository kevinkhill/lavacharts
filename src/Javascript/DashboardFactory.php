<?php

namespace Khill\Lavacharts\Javascript;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Configs\DataTable;
use \Khill\Lavacharts\Dashboard\Dashboard;
use \Khill\Lavacharts\Exceptions\InvalidElementId;

/**
 * DashboardGenerator Class
 *
 * This class takes Chart and Control Wrappers and uses all of the info to build the complete
 * javascript blocks for outputing into the page.
 *
 * @category   Class
 * @package    Lavacharts
 * @subpackage Javascript
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class DashboardFactory extends JavascriptFactory
{
    /**
     * Dashboard to generate javascript from.
     *
     * @var \Khill\Lavacharts\Dashboard\Dashboard
     */
    private $dashboard;


    /**
     * Creates a new ChartFactory with the javascript template.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to process
     * @param  \Khill\Lavacharts\Values\ElementId $elementId HTML element id to output into.
     * @return self
     */
    public function __construct(Dashboard $dashboard, ElementId $elementId)
    {
        $this->dashboard    = $dashboard;
        $this->elementId    = $elementId;
        $this->template     = $this->getTemplate();
        $this->templateVars = $this->getTemplateVars();
    }

    /**
     * Builds the Javascript code block for a Dashboard
     *
     * @since  3.0.0
     * @access private
     * @param  \Khill\Lavacharts\Dashboard\Dashboard $dashboard
     * @return string Javascript code block.
     */
    private function getTemplateVars()
    {
        $dashboard = $this->dashboard;
        //$boundChart = $this->dashboard->getBinding('MyPie')->getChartWrapper()->getChart();
        $boundCharts = $this->dashboard->getBoundCharts();
        //dd($boundCharts);

        $vars = [
            'label'     => (string) $this->dashboard->getLabel(),
            'version'   => $dashboard::VERSION,
            'class'     => $dashboard::VIZ_CLASS,
            'packages'  => [
                $dashboard::VIZ_PACKAGE,
                //$boundChart::VIZ_PACKAGE
            ],
            //'chartData' => $boundChart->getDataTableJson(),
            'elemId'    => (string) $this->elementId,
            'bindings'  => $this->processBindings(),
            'dataVer'   => DataTable::VERSION,
            'dataClass' => DataTable::VIZ_CLASS,
        ];

        foreach ($boundCharts as $chart) {
            $vars['chartData'] = $chart->getDataTableJson();

            array_push($vars['packages'], $chart::VIZ_PACKAGE);
        }

        $vars['packages'] = json_encode($vars['packages']);

        return $vars;
    }

    /**
     * Process the charts to retrieve the datatables for a Dashboard.
     *
     * Turns the charts' datatables into new Google DataTable Objects.
     *
     * @since  3.0.0
     * @access public
     * @param  \Khill\Lavacharts\Dashboard\Dashboard $dashboard
     * @return string
     */
    public function processCharts(Dashboard $dashboard)
    {
        $output = '';
    }

    /**
     * Process all the bindings for a Dashboard.
     *
     * Turns the chart and control wrappers into new Google Vizualization Objects.
     *
     * @since  3.0.0
     * @access public
     * @return string
     */
    public function processBindings()
    {
        $output = '';
        $bindings = $this->dashboard->getBindings();

        foreach ($bindings as $binding) {
            $bindingHash = spl_object_hash($binding);

            $chartWrapper   = $binding->getChartWrappers()[0];
            $controlWrapper = $binding->getControlWrappers()[0];

            $control = sprintf('"control": new %s(%s)', $controlWrapper::VIZ_CLASS, $controlWrapper->toJson());
            $chart   = sprintf('"chart"  : new %s(%s)', $chartWrapper::VIZ_CLASS,   $chartWrapper->toJson());

            $output .= sprintf('$this.bindings["%s"] = {%s, %s};',
                                  $bindingHash,
                                  $control,
                                  $chart
                              ).PHP_EOL;

            $output .= sprintf('            '.
                '$this.dashboard.bind($this.bindings["%1$s"]["control"], $this.bindings["%1$s"]["chart"]);',
                $bindingHash
            );
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
        return
<<<'DASH'
        //Checking if dashboard div exists
        if (! document.getElementById("<elemId>")) {
            throw new Error('[Lavacharts] No matching element was found with ID "<elemId>"');
        }

        lava.dashboards["<label>"] = new lava.Dashboard();

        lava.dashboards["<label>"].render = function() {
            var $this = lava.dashboards["<label>"];

            $this.dashboard = new <class>(document.getElementById('<elemId>'));

            $this.data = new <dataClass>(<chartData>, <dataVer>);

            <bindings>

            $this.dashboard.draw($this.data);
        };

        google.load('visualization', '<version>', {'packages':<packages>});
        google.setOnLoadCallback(function() {
            lava.dashboards["<label>"].render();
            lava.readyCallback();
        });

        //lava.register("<chartType>", "<chartLabel>");
DASH;
    }

}
