<?php

namespace Khill\Lavacharts\Javascript;

use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Configs\Renderable;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Dashboards\Dashboard;

/**
 * ScriptManager Class
 *
 * This class takes charts and uses all the info to build the complete
 * javascript blocks for outputting into the page. Also will output the lava.js module
 * and track if it is in page or not.
 *
 * @category   Class
 * @package    Khill\Lavacharts
 * @subpackage Javascript
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ScriptManager
{
    /**
     * Directory to javascript sources.
     *
     * @var string
     */
    const JS_DIR = '/../../javascript/';

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
     * Lava.js module location.
     *
     * @var string
     */
    const LAVA_JS = 'dist/lava.js';

    /**
     * Tracks if the lava.js module and jsapi have been rendered.
     *
     * @var bool
     */
    protected $lavaJsRendered = false;

    /**
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Returns true|false depending on if the lava.js module
     * has be output to the page
     *
     * @return boolean
     */
    public function lavaJsRendered()
    {
        return $this->lavaJsRendered;
    }

    /**
     * Gets the lava.js module.
     *
     * @return string Javascript code blocks.
     */
    public function getLavaJsModule()
    {
        $lavaJs = realpath(__DIR__ . self::JS_DIR . self::LAVA_JS);

        $this->lavaJsRendered = true;

        return self::scriptTagWrap(file_get_contents($lavaJs));
    }

    /**
     * Return the javascript of a renderable resource.
     *
     * @return string Javascript blocks
     */
    public function getJavascript(Renderable $renderable)
    {
        if ($renderable instanceof Dashboard) {
            return (new DashboardJsFactory($renderable))->getJavascript();
        }

        if ($renderable instanceof Chart) {
            return (new ChartJsFactory($renderable))->getJavascript();
        }
    }

    /**
     * Wraps javascript within an html script tag
     *
     * @param  string $javascript
     * @return string HTML script tag with javascript
     */
    public static function scriptTagWrap($javascript)
    {
        return PHP_EOL . self::JS_OPEN . PHP_EOL . $javascript . PHP_EOL . self::JS_CLOSE;
    }
}
