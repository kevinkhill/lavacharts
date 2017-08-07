<?php

namespace Khill\Lavacharts\Dashboards\Wrappers;

use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\Contracts\JsClass;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Contracts\Wrappable;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Khill\Lavacharts\Support\Traits\HasElementIdTrait as HasElementId;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;
use Khill\Lavacharts\Support\StringValue as Str;

/**
 * Class Wrapper
 *
 * The control and chart wrappers extend this for common methods.
 *
 *
 * @package   Khill\Lavacharts\Dashboards\Wrappers
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
abstract class Wrapper implements Arrayable, Javascriptable, Jsonable, JsClass
{
    use HasElementId, ArrayToJson, ToJavascript;

    /**
     * The contents of the wrap, either Chart or Filter.
     *
     * @var Wrappable
     */
    protected $contents;

    /**
     * The renderable's unique elementId.
     *
     * @var string
     */
    protected $elementId;

    /**
     * Returns a string of the javascript visualization class for the Wrapper.
     *
     * @return string
     */
    abstract public function getJsClass();

    /**
     * Builds a new Wrapper object.
     *
     * @param Wrappable $wrappable
     * @param string    $elementId
     * @internal param Wrappable $itemToWrap
     */
    public function __construct(Wrappable $wrappable, $elementId)
    {
        $this->contents = $wrappable;

        $this->setElementId($elementId);
    }

    /**
     * Unwraps and returns the wrapped object.
     *
     * @return Wrappable
     */
    public function unwrap()
    {
        return $this->contents;
    }

    /**
     * Array representation of the Wrapper
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'containerId' => $this->elementId,
            'options'     => $this->contents->getOptions(),
            $this->contents->getWrapType() => $this->contents->getType()
        ];
    }

    /**
     * Return a format string that will be used by vsprintf to convert the
     * extending class to javascript.
     *
     * @return string
     */
    public function getJavascriptFormat() //TODO: remove?
    {
        return 'new %s(%s)';
    }

    /**
     * Return an array of arguments to pass to the format string provided
     * by getJavascriptFormat().
     *
     * These variables will be used with vsprintf, and the format string
     * to convert the extending class to javascript.
     *
     * @return array
     */
    public function getJavascriptSource()
    {
        return [$this->getJsClass(), $this->toJson()];
    }
}
