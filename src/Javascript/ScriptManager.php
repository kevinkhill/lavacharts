<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Exceptions\InvalidElementIdException;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Support\Renderable;

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
    private $lavaJsLoaded = false;

    /**
     * Wraps a buffer with an html script tag
     *
     * @param Buffer $buffer
     * @return Buffer
     */
    public static function scriptTagWrap(Buffer $buffer)
    {
        return $buffer
//            ->prepend(PHP_EOL)
            ->prepend(static::JS_OPEN)
//            ->prepend(PHP_EOL)
//            ->append(PHP_EOL)
            ->append(static::JS_CLOSE);
    }

    /**
     * ScriptManager constructor.
     *
     * @param array $options
     */
    function __construct($options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Returns true|false depending on if the lava.js module
     * has be output to the page
     *
     * @return boolean
     */
    public function lavaJsLoaded()
    {
        return $this->lavaJsLoaded;
    }

    /**
     * Gets the lava.js module.
     *
     * @param \Khill\Lavacharts\Support\Options $options
     * @return \Khill\Lavacharts\Support\Buffer
     * @internal param array $config
     */
    public function getLavaJs(Options $options)
    {
        $this->lavaJsLoaded = true;

        $buffer = $this->getLavaJsSource();

        $buffer->pregReplace('/OPTIONS_JSON/', $options->toJson());

        return static::scriptTagWrap($buffer);
    }

    /**
     * Returns a buffer with the javascript of a renderable resource.
     *
     * @param  Renderable $renderable
     * @return Buffer
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementIdException
     */
    public function getJavascriptBuffer(Renderable $renderable)
    {
        if (! $renderable->hasElementId()) {
            throw new InvalidElementIdException($renderable);
        }

        $buffer = new Buffer($renderable);

        /** Converting string dates to date constructors */
        $buffer->pregReplace('/"Date\(((:?[0-9]+,?)+)\)"/', 'new Date(\1)');

        /** Converting string nulls to actual nulls */
        $buffer->pregReplace('/"null"/', 'NULL');

//        return $this->scriptTagWrap($buffer);
        return $buffer;
    }

    /**
     * Get the source of the lava.js module as a Buffer
     *
     * @return Buffer
     */
    private function getLavaJsSource()
    {
        $lavaJs = realpath(__DIR__ . self::LAVA_JS);

        return new Buffer(file_get_contents($lavaJs));
    }
}
