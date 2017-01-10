<?php

namespace Khill\Lavacharts\Dashboards\Filters;

/**
 * Category Filter Class
 *
 * A picker to choose one or more between a set of defined values.
 *
 * @package   Khill\Lavacharts\Dashboards\Filters
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 * @see       https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationcategoryfilter
 */
class CategoryFilter extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'CategoryFilter';
}
