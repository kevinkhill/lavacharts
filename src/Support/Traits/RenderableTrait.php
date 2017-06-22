<?php

namespace Khill\Lavacharts\Support\Traits;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Support\Traits\ElementIdTrait as HasElementId;

/**
 * Trait RenderableTrait
 *
 * This class is the parent to charts, dashboards, and controls since they
 * will need to be rendered onto the page.
 *
 * @package    Khill\Lavacharts\Support
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
trait RenderableTrait
{
    /**
     * The renderable's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    protected $label;

    /**
     * The renderable's unique elementId.
     *
     * @var \Khill\Lavacharts\Values\ElementId
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
     * @return \Khill\Lavacharts\Values\ElementId
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * Returns the ElementId as a string.
     *
     * @return string
     */
    public function getElementIdStr()
    {
        return (string) $this->elementId;
    }

    /**
     * Creates and/or sets the ElementId.
     *
     * @param  string|\Khill\Lavacharts\Values\ElementId $elementId
     * @throws \Khill\Lavacharts\Exceptions\InvalidElementId
     */
    public function setElementId($elementId)
    {
        if ($elementId instanceof ElementId) {
            $this->elementId = $elementId;
        } else {
            $this->elementId = new ElementId($elementId);
        }
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
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the label as a string.
     *
     * @return string
     */
    public function getLabelStr()
    {
        return (string) $this->label;
    }

    /**
     * Creates and/or sets the Label.
     *
     * @param  string|\Khill\Lavacharts\Values\Label $label
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function setLabel($label)
    {
        if ($label instanceof Label) {
            $this->label = $label;
        } else {
            $this->label = new Label($label);
        }
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
     * Get the type of renderable class
     *
     * @since 3.2.0
     * @return string
     */
    public function getRenderableType()
    {
        static::class;
    }

    /**
     * Sets the renderable status of the Chart
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
