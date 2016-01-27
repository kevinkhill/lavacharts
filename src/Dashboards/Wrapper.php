<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Dashboards\Filters\Filter;
use \Khill\Lavacharts\Values\ElementId;

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
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Wrapper implements \JsonSerializable
{
    /**
     * ContainerId of the div to render into.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    protected $containerId;

    /**
     * Chart or Filter that is wrapped.
     *
     * @var \Khill\Lavacharts\Charts\Chart|\Khill\Lavacharts\Dashboards\Filters\Filter
     */
    protected $wrappedObject;

    /**
     * Builds a new Wrapper object.
     *
     * @param  \Khill\Lavacharts\Values\ElementId $containerId
     */
    public function __construct(ElementId $containerId)
    {
        $this->containerId = $containerId;
    }

    /**
     * Returns the container id.
     *
     * @return \Khill\Lavacharts\Values\ElementId
     */
    public function getContainerId()
    {
        return $this->containerId;
    }

    /**
     * Unwraps and returns the wrapped object.
     *
     * @return \Khill\Lavacharts\Charts\Chart|\Khill\Lavacharts\Dashboards\Filters\Filter
     */
    public function unwrap()
    {
        return $this->wrappedObject;
    }

    /**
     * Custom serialization of the ChartWrapper.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->wrappedObject instanceof Chart) {
            $type = 'chartType';
        }

        if ($this->wrappedObject instanceof Filter) {
            $type = 'controlType';
        }

        return [
            $type         => $this->wrappedObject->getType(),
            'containerId' => (string) $this->containerId,
            'options'     => $this->wrappedObject
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
