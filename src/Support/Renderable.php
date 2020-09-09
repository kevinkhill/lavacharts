<?php

namespace Khill\Lavacharts\Support;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Support\Traits\ElementIdTrait as HasElementId;

/**
 * Renderable Class
 *
 * This class is the parent to charts, dashboards, and controls since they
 * will need to be rendered onto the page.
 *
 * @package   Khill\Lavacharts\Support
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @license   http://opensource.org/licenses/MIT MIT
 * @link      http://lavacharts.com                   Official Documentation
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @since     3.1.0
 */
class Renderable
{
    use HasElementId;

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
     * Sets the renderable's ElementId or generates on from a string
     *
     * @param \Khill\Lavacharts\Values\Label     $label     Label of the chart
     * @param \Khill\Lavacharts\Values\ElementId $elementId Element ID in the page
     */
    public function __construct(Label $label, ElementId $elementId = null)
    {
        $this->label = $label;

        if ($elementId === null) {
            $this->elementId = $this->_generateElementId();
        } else {
            $this->elementId = $elementId;
        }
    }

    /**
     * Creates and/or sets the Label.
     *
     * @param \Khill\Lavacharts\Values\Label $label Label of the chart
     *
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @return void
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
     * Returns the label.
     *
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the label.
     *
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabelStr()
    {
        return (string) $this->label;
    }

    /**
     * Generate an ElementId
     *
     * This method removes invalid characters from the chart label
     * to use as an elementId.
     *
     * @access private
     * @link   http://stackoverflow.com/a/11330527/2503458
     * @return string
     */
    private function _generateElementId()
    {
        $string = strtolower((string) $this->label);
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", "-", $string);

        return $string;
    }
}
