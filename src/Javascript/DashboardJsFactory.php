<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Dashboards\Dashboard;
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
    const OUTPUT_TEMPLATE = 'dashboard.tmpl.js';

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

        parent::__construct(self::OUTPUT_TEMPLATE);
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
            'elemId'    => $this->dashboard->getElementIdStr(),
            'label'     => $this->dashboard->getLabelStr(),
            'version'   => Dashboard::VERSION,
            'class'     => $this->dashboard->getJsClass(),
            'packages'  => [
                $this->dashboard->getJsPackage()
            ],
            'bindings' => $this->processBindings(),
            'chartData' =>$this->dashboard->getDataTableJson()
        ];

        /** @var \Khill\Lavacharts\Charts\Chart $chart */
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
    private function processBindings()
    {
        $buffer = '';
        $bindings = $this->dashboard->getBindings();

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        foreach ($bindings as $binding) {
            switch ($binding::TYPE) {
                case 'OneToOne':
                    $controls = $binding->getControlWrappers()[0]->getJsConstructor();
                    $charts   = $binding->getChartWrappers()[0]->getJsConstructor();
                    break;

                case 'OneToMany':
                    $controls = $binding->getControlWrappers()[0]->getJsConstructor();
                    $charts   = $this->mapWrapperArray($binding->getChartWrappers());
                    break;

                case 'ManyToOne':
                    $controls = $this->mapWrapperArray($binding->getControlWrappers());
                    $charts   = $binding->getChartWrappers()[0]->getJsConstructor();
                    break;

                case 'ManyToMany':
                    $controls = $this->mapWrapperArray($binding->getControlWrappers());
                    $charts   = $this->mapWrapperArray($binding->getChartWrappers());
                    break;
            }

            $buffer .= sprintf('this.dashboard.bind(%s, %s);', $controls, $charts);
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
        $wrappers = array_map(function ($wrapperArray) {
            return $wrapperArray->getJsConstructor();
        }, $wrapperArray);

        return '[' . implode(', ', $wrappers) . ']';
    }
}
