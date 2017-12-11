<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Exceptions\InvalidElementIdException;
use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Volcano;

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
class ScriptManager implements Customizable
{
    use HasOptions;

    /**
     * Lava.js module location.
     *
     * @var string
     */
    const LAVA_JS = '/../../lavajs/dist/lava.js';

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
     * Script output buffer.
     *
     * @var Buffer
     */
    private $outputBuffer;

    /**
     * Tracks if the lava.js module and google loader have been output.
     *
     * @var bool
     */
    private $lavaJsLoaded = false;

    /**
     * Status of whether the scripts have been output to the page.
     *
     * @var bool
     */
    private $scriptsOutput = false;

    /**
     * Instance of the Volcano to use for rendering.
     *
     * @var Volcano
     */
    private $volcano;

    /**
     * ScriptManager constructor.
     */
    function __construct()
    {
        $this->outputBuffer = new Buffer();
    }

    /**
     * Set the instance of the Volcano to use when rendering charts.
     *
     * @param Volcano $volcano
     */
    public function setVolcano(Volcano $volcano)
    {
        $this->volcano = $volcano;
    }

    /**
     * Renders all charts and dashboards that have been defined.
     *
     * TODO: this fails silently if the chart doesn't have an elementId
     *
     * @since 4.0.0
     * @return string <script> tags
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementIdException
     */
    public function getScriptTags()
    {
        if (! $this->lavaJsLoaded()) {
            $this->loadLavaJs();
        }

        if ($this->volcano->count() > 0) {
            $this->openScriptTag();

            /** @var Renderable $renderable */
            foreach ($this->volcano as $renderable) {
                if ($renderable->isRenderable()) {
                    $this->addRenderableToOutput($renderable);
                }
            }

            $this->closeScriptTag();
        }

        return $this->getOutputBuffer();
    }

    /**
     * Disable the output of the lava.js module <script> block
     *
     * Calling this method before rendering will override the output of the lava.js
     * module into the page. The 'auto_run' option is also set to false, since we
     * are now relying on the user to load the lava.js module manually.
     *
     * @since 3.1.9
     * @return void
     */
    public function bypassLavaJsOutput()
    {
        $this->lavaJsLoaded = true;

        $this->options->set('auto_run', false);
    }

    /**
     * Returns true|false depending on if the lava.js module
     * and renderables have been output to the page.
     *
     * @return bool
     */
    public function scriptsOutput()
    {
        return $this->scriptsOutput;
    }

    /**
     * Returns true|false depending on if the lava.js module
     * has be output to the page
     *
     * @return bool
     */
    public function lavaJsLoaded()
    {
        return $this->lavaJsLoaded;
    }

    /**
     * Appends an opening script tag to the output buffer.
     *
     * @since 4.0.0
     * @return self
     */
    public function openScriptTag()
    {
        $this->outputBuffer->append(static::JS_OPEN);

        return $this;
    }

    /**
     * Appends a closing script tag to the output buffer.
     *
     * @since 4.0.0
     * @return self
     */
    public function closeScriptTag()
    {
        $this->outputBuffer->append(static::JS_CLOSE);

        return $this;
    }

    /**
     * Returns the output buffer of the ScriptManager.
     *
     * @since 4.0.0
     * @return Buffer
     */
    public function getOutputBuffer()
    {
        return $this->outputBuffer;
    }

    /**
     * Initialize the output buffer with the Lava.js module.
     *
     * @since 4.0.0
     */
    public function loadLavaJs()
    {
        $this->outputBuffer = $this->getLavaJs();
    }

    /**
     * Gets the lava.js module.
     *
     * @return Buffer
     */
    public function getLavaJs()
    {
        $this->lavaJsLoaded = true;

        $buffer = new ScriptBuffer($this->getLavaJsSource());

        $buffer->pregReplace('/__OPTIONS__/', $this->options->toJson());

        return $buffer;
    }

    /**
     * Add a renderable to the output buffer.
     *
     * @since 4.0.0
     * @param \Khill\Lavacharts\Support\Renderable $renderable
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementIdException
     */
    public function addRenderableToOutput(Renderable $renderable)
    {
        if (! $renderable->hasElementId()) {
            throw new InvalidElementIdException($renderable);
        }

        $buffer = new Buffer($renderable);

        /** Converting string dates to date constructors */
        $buffer->pregReplace('/"Date\(((:?[0-9]+,?)+)\)"/', 'new Date(\1)');

        /** Converting string nulls to actual nulls */
        $buffer->pregReplace('/"null"/', 'NULL');

        $this->outputBuffer->append($buffer);
    }

    /**
     * Get the source of the lava.js module as a Buffer
     *
     * @return string
     */
    private function getLavaJsSource()
    {
        $lavaJs = realpath(__DIR__ . self::LAVA_JS);

        return file_get_contents($lavaJs);
    }
}
