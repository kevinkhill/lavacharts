<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidFilter;
use Khill\Lavacharts\Exceptions\InvalidFilterType;
use Khill\Lavacharts\Exceptions\InvalidParamType;
use Khill\Lavacharts\Support\Args;
use Khill\Lavacharts\Support\Arr;

/**
 * FilterFactory creates new filters for use in a dashboard.
 *
 *
 * @package   Khill\Lavacharts\Dashboards\Filters
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class FilterFactory
{
    /**
     * Create a new Filter.
     *
     * @param  string $type
     * @param  array  $args
     * @return Filter
     * @throws \Khill\Lavacharts\Exceptions\InvalidFilterType
     */
    public static function create($type, $args)
    {
        $args = new Args($args); // TODO: keep?

        $labelOrIndex = $args->verify(0, ['string', 'int']);
        $options = $args->verify(1, 'array', []);

        //TODO: Delete this

    }

    /**
     * Build the namespace to create a new filter.
     *
     * @param  string $filter
     * @return string
     */
    private static function makeNamespace($filter)
    {
        if (strpos($filter, 'range') !== false) {
            $filter = ucfirst(str_replace('range', 'Range', $filter));
        }

        return __NAMESPACE__ . '\\' . $filter . 'Filter';
    }
}
