<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Exceptions\ElementIdException;
use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Contracts\RenderableInterface as Renderable;

/**
 * ScriptManager Class
 *
 * This class takes charts and uses all the info to build the complete
 * javascript blocks for outputting into the page. Also will output the lava.js module
 * and track if it is in page or not.
 *
 * @category   Class
 * @package    Khill\Lavacharts\Javascript
 * @since      3.0.5
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class ScriptManager
{
    use HasOptions;

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
    private $lavaJsRendered = false;

    /**
     * ScriptManager constructor.
     *
     * @param Options $options
     */
    function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * Calling this method before rendering will override the output of the lava.js
     * module into the page. The 'auto_run' option is also set to false, since we
     * are now relying on the user to load the lava.js module manually.
     */
    public function bypassLavaJsOutput()
    {
        $this->lavaJsRendered = true;

        $this->options->set('auto_run', false);
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
     * @param  array $config
     * @return \Khill\Lavacharts\Support\Buffer
     */
    public function getLavaJsModule(array $config = [])
    {
        $lavaJs = realpath(__DIR__ . self::LAVA_JS);
        $buffer = new Buffer(file_get_contents($lavaJs));

        $this->options->merge($config);

        $buffer->pregReplace('/OPTIONS_JSON/', $this->options->toJson());

        $this->lavaJsRendered = true;

        return $this->scriptTagWrap($buffer);
    }

    /**
     * Returns a buffer with the javascript of a renderable resource.
     *
     *
     * @param  \Khill\Lavacharts\Support\Contracts\RenderableInterface $renderable
     * @return \Khill\Lavacharts\Support\Buffer
     * @throws \Khill\Lavacharts\Exceptions\ElementIdException
     */
    public function getOutputBuffer(Renderable $renderable)
    {
        if ($renderable->hasElementId() === false) {
            throw new ElementIdException($renderable);
        }

        if ($renderable instanceof Dashboard) {
            $jsFactory = new DashboardJsFactory($renderable);
        }

        if ($renderable instanceof Chart) {
            $jsFactory = new ChartJsFactory($renderable);
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
