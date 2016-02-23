<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;

/**
 * Renderable Class
 *
 * This class is the parent to charts, dashboards, and controls since they
 * will need to be rendered onto the page.
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Renderable
{
    /**
     * The chart's unique elementId.
     *
     * @var \Khill\Lavacharts\Values\ElementId
     */
    protected $elementId;

    /**
     * Sets the renderable's ElementId or generates on from a string
     *
     * @param \Khill\Lavacharts\Values\ElementId $elementId
     */
    public function __construct(ElementId $elementId = null)
    {
        if ($elementId === null) {
            $elementId = $this->generateElementId($this->label);

            $noticeMsg = 'No ElementId was set for '.static::TYPE.'("'.$this->label.'"), using "'.$elementId.'".';
            trigger_error($noticeMsg, E_USER_NOTICE);
        }

        $this->elementId = $elementId;
    }

    /**
     * Sets the ElementId
     *
     * @param \Khill\Lavacharts\Values\ElementId $elementId
     */
    public function setElementId(ElementId $elementId)
    {
        $this->elementId = $elementId;
    }

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
     * Generate an ElementId
     *
     * This method removes invalid characters from the chart label
     * to use as an elementId.
     *
     * @link http://stackoverflow.com/a/11330527/2503458
     *
     * @param  string $string String from which to generate an ID.
     */
    private function generateElementId(Label $label)
    {
        $string = strtolower((string) $label);
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", "-", $string);

        return new ElementId($string);
    }
}
