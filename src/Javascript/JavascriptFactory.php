<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Dashboards\Dashboard;

/**
 * JavascriptFactory Class
 *
 * This class takes charts and uses all the info to build the complete
 * javascript blocks for outputting into the page.
 *
 * @category   Class
 * @package    Khill\Lavacharts
 * @subpackage Javascript
 * @since      2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class JavascriptFactory
{
    /**
     * Opening javascript tag.
     *
     * @var string
     */
    const JS_OPEN = '<script type="text/javascript">';

    /**
     * Closing javascript tag.
     *
     * @var string
     */
    const JS_CLOSE = '</script>';

    /**
     * Script block for the Google's Chart API.
     *
     * @var string
     */
    const JSAPI = '<script type="text/javascript" src="//www.google.com/jsapi"></script>';

    /**
     * Javascript output.
     *
     * @var string
     */
    protected $out;

    /**
     * HTML element id to output the chart into.
     *
     * @var string
     */
    protected $elementId;

    /**
     * Javascript template for output.
     *
     * @var string
     */
    protected $template;

    /**
     * Map of template vars to values.
     *
     * @var array
     */
    protected $templateVars;

    /**
     * Tracks if the lava js core and jsapi have been rendered.
     *
     * @var bool
     */
    protected $coreJsRendered = false;

    /**
     * Returns true|false depending on if the jsapi & lava.js core
     * have been added to the output.
     *
     * @access public
     * @return boolean
     */
    public function coreJsRendered()
    {
        return $this->coreJsRendered;
    }

    /**
     * Gets the Google chart api and lava.js core.
     *
     * @access public
     * @return string Javascript code blocks.
     */
    public function getCoreJs()
    {
        //$coreJs  = self::JSAPI;
        $coreJs  = self::JS_OPEN;
        $coreJs .= file_get_contents(__DIR__.'/../../javascript/lava.js');
        $coreJs .= self::JS_CLOSE;

        $this->coreJsRendered = true;

        return $coreJs;
    }

    /**
     * Parses the javascript template and wraps the output in a script tag.
     *
     * @access public
     * @return string Javascript code block.
     */
    public function getJavascript()
    {
        $this->parseTemplate();

        return $this->scriptTagWrap($this->out);
    }

    /**
     * Checks for an element id to output the chart into and builds the Javascript.
     *
     * @access public
     * @uses   \Khill\Lavacharts\Charts\Chart
     * @param  \Khill\Lavacharts\Charts\Chart     $chart Chart to render.
     * @param  \Khill\Lavacharts\Values\ElementId $elementId HTML element id to output the chart into.
     * @return string Javascript code block.
     */
    public function getChartJs(Chart $chart, ElementId $elementId)
    {
        return (new ChartFactory($chart, $elementId))->getJavascript();
    }

    /**
     * Checks for an element id to output the chart into and builds the Javascript.
     *
     * @since  3.0.0
     * @access public
     * @uses   \Khill\Lavacharts\Dashboards\Dashboard
     * @param  \Khill\Lavacharts\Dashboards\Dashboard $dashboard Dashboard to render.
     * @param  \Khill\Lavacharts\Values\ElementId     $elementId HTML element id to output the dashboard into.
     * @return string Javascript code block.
     */
    public function getDashboardJs(Dashboard $dashboard, ElementId $elementId)
    {
        return (new DashboardFactory($dashboard, $elementId))->getJavascript();
    }

    /**
     * Parses the nowdoc javascript templates with the value mappings
     *
     * @return string Javascript
     */
    protected function parseTemplate()
    {
        $this->out = $this->template;

        foreach ($this->templateVars as $key => $value) {
            $this->out = preg_replace("/<$key>/", $value, $this->out);
        }
    }

    /**
     * Wraps javascript within an html script tag
     *
     * @param  string $javascript
     * @return string HTML script tag with javascript
     */
    protected function scriptTagWrap($javascript)
    {
        return PHP_EOL . self::JS_OPEN . PHP_EOL . $javascript . PHP_EOL . self::JS_CLOSE;
    }
}
