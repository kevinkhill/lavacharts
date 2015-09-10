<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Dashboards\Filters\Filter;

/**
 * ControlWrapper Class
 *
 * Used for building controls for dashboards.
 *
 * @package    Khill\Lavacharts
 * @subpackage Dashboards
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ControlWrapper extends Wrapper
{
    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.ControlWrapper';

    /**
     * Builds a ControlWrapper object.
     *
     * @param  \Khill\Lavacharts\Dashboards\Filters\Filter $filter
     * @param  \Khill\Lavacharts\Values\ElementId          $containerId
     */
    public function __construct(Filter $filter, ElementId $containerId)
    {
        $this->wrappedObject = $filter;

        parent::__construct($containerId);
    }
}
