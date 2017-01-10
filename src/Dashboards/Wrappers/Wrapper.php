<?php

namespace Khill\Lavacharts\Dashboards\Wrappers;

use Khill\Lavacharts\Values\ElementId;
use Khill\Lavacharts\Support\Traits\ElementIdTrait as HasElementId;
use Khill\Lavacharts\Support\Contracts\WrappableInterface as Wrappable;
use Khill\Lavacharts\Support\Contracts\JsonableInterface as Jsonable;
use Khill\Lavacharts\Support\Contracts\JsClassInterface as JsClass;

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
class Wrapper implements \JsonSerializable, Jsonable, JsClass
{
    use HasElementId;

    /**
     * The contents of the wrap, either Chart or Filter.
     *
     * @var \Khill\Lavacharts\Support\Contracts\WrappableInterface
     */
    protected $contents;

    /**
     * The renderable's unique elementId.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    protected $elementId;

    /**
     * Builds a new Wrapper object.
     *
     * @param \Khill\Lavacharts\Support\Contracts\WrappableInterface $itemToWrap
     * @param \Khill\Lavacharts\Values\ElementId                     $elementId
     */
    public function __construct(Wrappable $itemToWrap, ElementId $elementId)
    {
        $this->contents  = $itemToWrap;
        $this->elementId = $elementId;
    }

    /**
     * Unwraps and returns the wrapped object.
     *
     * @return \Khill\Lavacharts\Support\Contracts\WrappableInterface
     */
    public function unwrap()
    {
        return $this->contents;
    }

    /**
     * Custom serialization of the Wrapper.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'options'     => $this->contents,
            'containerId' => (string) $this->elementId,
            $this->contents->getWrapType() => $this->contents->getType()
        ];
    }

    /**
     * Returns the JSON serialized version of the Wrapper.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Returns a javascript string of the visualization class for the Wrapper.
     *
     * @return string
     */
    public function getJsClass()
    {
        return 'google.visualization.' . static::TYPE;
    }

    /**
     * Returns a javascript string with the constructor for the Wrapper.
     *
     * @return string
     */
    public function getJsConstructor()
    {
        return sprintf('new %s(%s)', $this->getJsClass(), $this->toJson());
    }
}
