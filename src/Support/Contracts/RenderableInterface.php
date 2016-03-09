<?php

namespace Khill\Lavacharts\Support\Contracts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;

/**
 * Interface RenderableInterface
 *
 * Defining the methods a class must have to be Renderable.
 *
 * @package    Khill\Lavacharts\Support
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
interface RenderableInterface
{
    /**
     * Sets the renderable's ElementId or generates on from a string
     *
     * @param \Khill\Lavacharts\Values\Label     $label
     * @param \Khill\Lavacharts\Values\ElementId $elementId
     */
    public function initRenderable(Label $label, ElementId $elementId = null);

    /**
     * Creates and/or sets the Label.
     *
     * @param  string|\Khill\Lavacharts\Values\Label $label
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     */
    public function setLabel($label);

    /**
     * Returns the label.
     *
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel();

    /**
     * Returns the label.
     *
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabelStr();

    /**
     * Generate an ElementId
     *
     * This method removes invalid characters from the chart label
     * to use as an elementId.
     *
     * @link http://stackoverflow.com/a/11330527/2503458
     * @access private
     */
    public function generateElementId();
}
