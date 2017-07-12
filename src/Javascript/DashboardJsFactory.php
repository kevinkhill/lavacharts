<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Dashboards\Wrappers\Wrapper;
use Khill\Lavacharts\Exceptions\RenderingException;
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
        $this->templateVars = $this->getTemplateVars();

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
            'chartData' =>$this->dashboard->getDataTable()->toJson()
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
     * @throws RenderingException
     */
    private function processBindings() //@TODO this could use some refactoring
    {
        if (! $this->dashboard->hasBindings()) {
            throw new RenderingException('Dashboards without bindings cannot be rendered.');
        }

        $buffer   = new Buffer();
        $bindings = $this->dashboard->getBindings();

        foreach ($bindings as $binding) {
            $buffer->append(
                $binding->toJavascript()
            );
        }

        return $buffer;
    }
}
