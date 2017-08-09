<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidFilterType;
use Khill\Lavacharts\Exceptions\InvalidParamType;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\Wrappable;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;

/**
 * Filter Parent Class
 *
 * The base class for the individual filter objects, providing common
 * functions to the child objects.
 *
 *
 * @package   Khill\Lavacharts\Dashboards\Filters
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Filter implements Customizable, Wrappable
{
    use HasOptions;

    /**
     * Valid filter types
     */
    const TYPES = [
        'CategoryFilter',
        'ChartRangeFilter',
        'DateRangeFilter',
        'NumberRangeFilter',
        'StringFilter'
    ];

    /**
     * Type of wrapper
     */
    const WRAP_TYPE = 'controlType';

    /**
     * The type of filter
     *
     * @var string
     */
    private $type;

    /**
     * Create a new filter object by named type
     *
     * @param string     $type
     * @param string|int $labelOrIndex
     * @param array      $options
     * @return mixed
     */
    public static function create($type, $labelOrIndex, array $options = [])
    {
        // Append 'Filter' because $lava->Filter('NumberRangeFilter', 1) is silly.
        if (is_string($type)) {
            $type = str_replace('Filter', '', $type);
        }

        // Check if valid filter type
        if (! in_array($type, Filter::TYPES, true)) {
            throw new InvalidFilterType($type);
        }

        return new static($labelOrIndex, $options);
    }

    /**
     * Builds a new Filter Object.
     *
     * Takes either a column label or a column index to filter. The options object will be
     * created internally, so no need to set defaults. The child filter objects will set them.
     *
     * @param  string|int $labelOrIndex
     * @param  array      $options Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidArgumentException
     */
    public function __construct($labelOrIndex, array $options = [])
    {
        if (! is_int($labelOrIndex) && ! is_string($labelOrIndex)) {
            throw new InvalidArgumentException($labelOrIndex, 'string | int');
        }

        if (is_int($labelOrIndex)) {
            $options = array_merge($options, [
                'filterColumnIndex' => $labelOrIndex
            ]);
        }

        if (is_string($labelOrIndex)) {
            $options = array_merge($options, [
                'filterColumnLabel' => $labelOrIndex
            ]);
        }

        $this->setOptions($options);
    }

    /**
     * Returns the type of filter
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns the Filter wrap type.
     *
     * @since 3.0.5
     * @return string
     */
    public function getWrapType()
    {
        return static::WRAP_TYPE;
    }
}
