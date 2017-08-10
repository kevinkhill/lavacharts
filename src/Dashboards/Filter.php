<?php

namespace Khill\Lavacharts\Dashboards;

use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidFilterType;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\Wrappable;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
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
class Filter implements /*Arrayable,*/ Customizable, Wrappable
{
    use HasOptions/*, ArrayToJson*/;

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
     * Create a new filter by named type and an array of args.
     *
     * The type will have 'Filter' removed (if it exists) and append back back.
     * This makes it so filters can be created by partial or full name.
     *
     * @param string $type
     * @param array  $args
     * @return Filter
     */
    public static function create($type, $args)
    {
        if (is_string($type)) {
            $type = str_replace('Filter', '', $type);
            $type = $type . 'Filter';
        }

        if (count($args) == 1) {
            return new static($type, $args[0]);
        }

        if (count($args) == 2) {
            return new static($type, $args[0], $args[1]);
        }
    }

    /**
     * Builds a new Filter Object.
     *
     * Takes either a column label or a column index to filter. The options object will be
     * created internally, so no need to set defaults. The child filter objects will set them.
     *
     * @param  string     $type
     * @param  string|int $labelOrIndex
     * @param  array      $options Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidArgumentException
     * @throws \Khill\Lavacharts\Exceptions\InvalidFilterType
     */
    public function __construct($type, $labelOrIndex, array $options = [])
    {
        if (! in_array($type, Filter::TYPES, true)) {
            throw new InvalidFilterType($type);
        }

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

        $this->type = $type;
        $this->setOptions($options);
    }

//    public function toArray()
//    {
//        return [
//            'type' => $this->getType(),
//            'wrapType' => $this->getWrapType(),
//            'options' => $this->options->toArray(),
//        ];
//    }

    /**
     * Returns the type of Filter
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
