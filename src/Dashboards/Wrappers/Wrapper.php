<?php

namespace Khill\Lavacharts\Dashboards\Wrappers;

use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Traits\ElementIdTrait as HasElementId;

/**
 * Wrapper Parent Class
 *
 * The control and chart wrappers extend this for common methods.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Wrapper implements \JsonSerializable
{
    use HasElementId;

    /**
     * Chart or Filter that is wrapped.
     *
     * @var \Khill\Lavacharts\Charts\Chart|\Khill\Lavacharts\Dashboards\Filters\Filter
     */
    protected $wrap;

    /**
     * The renderable's unique elementId.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    protected $elementId;

    /**
     * Builds a new Wrapper object.
     *
     * @param  \Khill\Lavacharts\Values\ElementId $elementId
     */
    public function __construct(ElementId $elementId)
    {
        $this->elementId = $elementId;
    }

    /**
     * Unwraps and returns the wrapped object.
     *
     * @return \Khill\Lavacharts\Charts\Chart|\Khill\Lavacharts\Dashboards\Filters\Filter
     */
    public function unwrap()
    {
        return $this->wrap;
    }

    /**
     * Custom serialization of the ChartWrapper.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'options'     => $this->wrap,
            'containerId' => (string) $this->elementId,
            $this->wrap->getWrapType() => $this->wrap->getType()
        ];
    }

    /**
     * Returns a javascript string with the constructor for the Wrapper.
     *
     * @return string
     */
    public function toJavascript()
    {
        return sprintf('new %s(%s)', static::VIZ_CLASS, json_encode($this));
    }
}
