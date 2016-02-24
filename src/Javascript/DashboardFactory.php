<?php

namespace Khill\Lavacharts\Javascript;

use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Dashboards\Dashboard;

/**
 * DashboardFactory Class
 *
 * This class takes Chart and Control Wrappers and uses all of the info to build the complete
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
class DashboardFactory extends JavascriptFactory
{
    /**
     * Dashboard to generate javascript from.
     *
     * @var \Khill\Lavacharts\Dashboards\Dashboard
     */
    private $dashboard;


    /**
     * Creates a new DashboardFactory with the javascript template.
     *
     * @access public
     * @param \Khill\Lavacharts\Dashboards\Dashboard $dashboard
     * @param  \Khill\Lavacharts\Values\ElementId    $elementId HTML element id to output into.
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
     * @access private
     * @return string Javascript code block.
     */
    private function getTemplateVars()
    {
        $boundCharts = $this->dashboard->getBoundCharts();

        $vars = [
            'label'     => (string) $this->dashboard->getLabel(),
            'version'   => Dashboard::VERSION,
            'class'     => Dashboard::VIZ_CLASS,
            'packages'  => [
                Dashboard::VIZ_PACKAGE
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

        $vars['packages'] = json_encode(array_unique($vars['packages']));

        return $vars;
    }

    /**
     * Process all the bindings for a Dashboard.
     *
     * Turns the chart and control wrappers into new Google Visualization Objects.
     *
     * @access public
     * @return string
     */
    public function processBindings()
    {
        $output = '';
        $bindings = $this->dashboard->getBindings();

        foreach ($bindings as $binding) {
            switch($binding::TYPE) {
                case 'OneToOne':
                    $controls = $binding->getControlWrappers()[0]->toJavascript();
                    $charts   = $binding->getChartWrappers()[0]->toJavascript();
                    break;

                case 'OneToMany':
                    $controls = $binding->getControlWrappers()[0]->toJavascript();
                    $charts   = $this->mapWrapperArray($binding->getChartWrappers());
                    break;

                case 'ManyToOne':
                    $controls = $this->mapWrapperArray($binding->getControlWrappers());
                    $charts   = $binding->getChartWrappers()[0]->toJavascript();
                    break;

                case 'ManyToMany':
                    $controls = $this->mapWrapperArray($binding->getControlWrappers());
                    $charts   = $this->mapWrapperArray($binding->getChartWrappers());
                    break;
            }

            $output .= sprintf('$this.dashboard.bind(%s, %s);', $controls, $charts);
        }

        return $output;
    }

    /**
     * Map the wrapper values from the array to javascript notation.
     *
     * @access private
     * @param  $wrapperArray Array of control or chart wrappers
     * @return string Json notation for the wrappers
     */
    private function mapWrapperArray($wrapperArray)
    {
        $wrappers = array_map(function ($wrapperArray) {
            return $wrapperArray->toJavascript();
        }, $wrapperArray);

        return '[' . implode(', ', $wrappers) . ']';
    }

    /**
     * Returns the dashboard javascript template.
     *
     * @access private
     * @return string Javascript template
     */
    private function getTemplate()
    {
        return <<<'DASH'
        lava.events.on('jsapi:ready', function (google) {
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

            google.load('visualization', '<version>', {
                packages: <packages>,
                callback: function() {
                    lava.dashboards["<label>"].render();
                }
            });

            //lava.register("<chartType>", "<chartLabel>");
        });
DASH;
    }

}
