<?php

namespace Khill\Lavacharts\Dashboards\Wrappers;

use Khill\Lavacharts\Values\ElementId;
use Khill\Lavacharts\Dashboards\Filters\Filter;

/**
 * ControlWrapper Class
 *
 * Used for building controls for dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Wrappers
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ControlWrapper extends Wrapper
{
    /**
     * Type of wrapper.
     *
     * @var string
     */
    const TYPE = 'ControlWrapper';

    /**
     * Builds a ControlWrapper object.
     *
     * @param  \Khill\Lavacharts\Dashboards\Filters\Filter $filter
     * @param  \Khill\Lavacharts\Values\ElementId          $containerId
     */
    public function __construct(Filter $filter, ElementId $containerId)
    {
        parent::__construct($filter, $containerId);
    }
}
