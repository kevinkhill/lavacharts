<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Dashboards\Dashboard;
//use Khill\Lavacharts\Support\Contracts\RenderableInterface as Renderable;
use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Values\ElementId;

/**
 * ScriptManager Class
 *
 * This class takes charts and uses all the info to build the complete
 * javascript blocks for outputting into the page. Also will output the lava.js module
 * and track if it is in page or not.
 *
 * @category   Class
 * @package    Khill\Lavacharts\Javascript
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
     * Lava.js module location.
     *
     * @var string
     */
    const LAVA_JS = '/../../javascript/dist/lava.js';

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
     * Tracks if the lava.js module and jsapi have been rendered.
     *
     * @var bool
     */
    protected $lavaJsRendered = false;

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
     * @return \Khill\Lavacharts\Support\Buffer
     */
    public function getLavaJsModule()
    {
        $lavaJs = realpath(__DIR__ . self::LAVA_JS);
        $buffer = new Buffer(file_get_contents($lavaJs));

        $this->lavaJsRendered = true;

        return $this->scriptTagWrap($buffer);
    }

    /**
     * Returns a buffer with the javascript of a renderable resource.
     *
     * @param  Chart|Dashboard                    $renderable
     * @param  \Khill\Lavacharts\Values\ElementId $elementId
     * @return \Khill\Lavacharts\Support\Buffer
     */
    public function getOutputBuffer($renderable, ElementId $elementId)
    {
        if ($renderable instanceof Dashboard) {
            $jsFactory = new DashboardJsFactory($renderable, $elementId);
        }

        if ($renderable instanceof Chart) {
            $jsFactory = new ChartJsFactory($renderable, $elementId);
        }

        $buffer = $jsFactory->getOutputBuffer();

        return $this->scriptTagWrap($buffer);
    }

    /**
     * Wraps a buffer with an html script tag
     *
     * @param \Khill\Lavacharts\Support\Buffer $buffer
     * @return \Khill\Lavacharts\Support\Buffer
     */
    private function scriptTagWrap(Buffer $buffer)
    {
        return $buffer->prepend(PHP_EOL)
                      ->prepend(self::JS_OPEN)
                      ->prepend(PHP_EOL)
                      ->append(PHP_EOL)
                      ->append(self::JS_CLOSE);
    }
}
