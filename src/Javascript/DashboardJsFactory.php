<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Dashboards\Wrappers\Wrapper;
use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Values\ElementId;

/**
 * DashboardFactory Class
 *
 * This class takes Chart and Control Wrappers and uses all of the info to build the complete
 * javascript blocks for outputting into the page.
 *
 * @category   Class
 * @package    Khill\Lavacharts\Javascript
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class DashboardJsFactory extends JavascriptFactory
{
    /**
     * Location of the output template.
     *
     * @var string
     */
    const JS_TEMPLATE = 'dashboard.tmpl.js';

    /**
     * Dashboard to generate javascript from.
     *
     * @var \Khill\Lavacharts\Dashboards\Dashboard
     */
    private $dashboard;

    /**
     * Creates a new DashboardFactory with the javascript template.
     *
     * @param \Khill\Lavacharts\Dashboards\Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
        $this->template  = self::JS_TEMPLATE;

        parent::__construct();
    }

    /**
     * Builds the template variables from the chart.
     *
     * @since  3.1.0
     * @access protected
     * @return array
     */
    protected function getTemplateVars()
    {
        $vars = [
            'version'   => $this->dashboard->getVersion(),
            'label'     => $this->dashboard->getLabel(),
            'elemId'    => $this->dashboard->getElementId(),
            'class'     => $this->dashboard->getJsClass(),
            'packages'  => [
                $this->dashboard->getJsPackage()
            ],
            'bindings' => $this->processBindings(),
            'chartData' =>$this->dashboard->getDataTableJson()
        ];

        foreach ($this->dashboard->getBoundCharts() as $chart) {
            array_push($vars['packages'], $chart->getJsPackage());
        }

        $vars['packages'] = json_encode(array_unique($vars['packages']));

        return $vars;
    }

    /**
     * Process all the bindings for a Dashboard.
     *
     * Turns the chart and control wrappers into new Google Visualization Objects.
     *
     * @access private
     * @return string
     */
    private function processBindings() //@TODO this could use some refactoring
    {
        $buffer = new Buffer();
        $bindings = $this->dashboard->getBindings();

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        foreach ($bindings as $binding) {
            switch ($binding->getType()) {
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

            $buffer->append(
                sprintf(
                    'this.dashboard.bind(%s, %s);',
                    $controls,
                    $charts
                )
            );
        }

        return $buffer;
    }

    /**
     * Map the wrapper values from the array to javascript notation.
     *
     * @access private
     * @param  array $wrapperArray Array of control or chart wrappers
     * @return string Json notation for the wrappers
     */
    private function mapWrapperArray($wrapperArray)
    {
        $wrappers = array_map(function (Wrapper $wrapper) {
            return $wrapper->toJavascript();
        }, $wrapperArray);

        return '[' . implode(', ', $wrappers) . ']';
    }
}
