<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\Values\ElementId;

/**
 * Wrapper Parent Class
 *
 * The control and chart wrappers extend this for common methods.
 *
 *
 * @package    Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
abstract class Wrapper implements \JsonSerializable
{
    /**
     * ContainerId of the div to render the control into.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    protected $containerId;

    /**
     * Builds a new Wrapper object.
     *
     * @param  \Khill\Lavacharts\Values\ElementId $containerId
     * @return self
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
     * Returns a javascript string with the constructor for the Wrapper.
     *
     * @return string
     */
    public function toJavascript()
    {
        return sprintf('new %s(%s)', static::VIZ_CLASS, json_encode($this));
    }
}
