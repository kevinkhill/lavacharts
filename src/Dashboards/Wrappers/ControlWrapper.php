<?php

namespace Khill\Lavacharts\Dashboards\Wrappers;

use Khill\Lavacharts\Dashboards\Filter;

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
     * Builds a ControlWrapper object.
     *
     * @param Filter $filter
     * @param string $containerId
     */
    public function __construct(Filter $filter, $containerId)
    {
        parent::__construct($filter, $containerId);
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return self::GOOGLE_VISUALIZATION . 'ControlWrapper';
    }
}
