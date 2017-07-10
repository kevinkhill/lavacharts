<?php

namespace Khill\Lavacharts\Support;

use JsonSerializable;
use Khill\Lavacharts\Exceptions\InvalidElementId;
use Khill\Lavacharts\Exceptions\InvalidLabel;
use Khill\Lavacharts\Javascript\ChartJsFactory;
use Khill\Lavacharts\Javascript\DashboardJsFactory;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Support\Traits\ElementIdTrait as HasElementId;
use Khill\Lavacharts\Support\StringValue as Str;

/**
 * Abstract Renderable Class
 *
 * This class is the parent to charts, dashboards, and controls since they
 * will need to be rendered onto the page.
 *
 * @package    Khill\Lavacharts\Support
 * @since      3.2.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
abstract class Renderable implements Arrayable, Jsonable
{
    use ArrayToJson;

    /**
     * The renderable's unique label.
     *
     * @var string
     */
    protected $label;

    /**
     * The renderable's unique elementId.
     *
     * @var string
     *
     */
    protected $elementId;

    /**
     * Defaulting to true so than all new Renderables can be rendered.
     *
     * @var bool
     */
    protected $renderable = true;

    /**
     * Returns the ElementId.
     *
     * @return string
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * Creates and/or sets the ElementId.
     *
     * @param  string $elementId
     * @throws InvalidElementId
     */
    public function setElementId($elementId)
    {
        $this->elementId = Str::verify($elementId);
    }

    /**
     * Check to see if the renderable has it's elementId set.
     *
     * @since  3.1.0
     * @return bool
     */
    public function hasElementId()
    {
        return isset($this->elementId);
    }

    /**
     * Returns the label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the Label.
     *
     * @param  string $label
     * @throws InvalidLabel
     */
    public function setLabel($label)
    {
        $this->label = Str::verify($label);
    }

    /**
     * Check to see if the renderable has it's label set.
     *
     * @since  3.2.0
     * @return bool
     */
    public function hasLabel()
    {
        return isset($this->label);
    }

    /**
     * Sets the renderable status
     *
     * @since  3.1.0
     * @param bool $renderable
     */
    public function setRenderable($renderable)
    {
        $this->isRenderable = (bool) $renderable;
    }

    /**
     * Returns the status of the renderability of the chart.
     *
     * @since  3.1.0
     * @return bool
     */
    public function isRenderable()
    {
        return $this->renderable && $this->hasLabel() && $this->hasElementId();
    }
}
