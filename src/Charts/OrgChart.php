<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Traits\PngRenderableTrait as PngRenderable;

/**
 * OrgChart Class
 *
 * Org charts are diagrams of a hierarchy of nodes, commonly used to
 * portray superior/subordinate relationships in an organization.
 * A family tree is a type of org chart.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     3.0.5
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class OrgChart extends Chart
{
    use PngRenderable;

    /**
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return 'orgchart';
    }
}
