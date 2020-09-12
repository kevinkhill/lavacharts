<?php

namespace Khill\Lavacharts\Dashboards;

use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidFilterType;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\Wrappable;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Support\StringValue as Str;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use ReflectionClass;

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
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Filter implements Arrayable, Customizable, Wrappable
{
    use HasOptions, ArrayToJson;

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
        $filter = new ReflectionClass(static::class);

        array_unshift($args, $type);

        return $filter->newInstanceArgs($args);
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
        $this->options = new Options($options);

        if (is_string($type)) {
            $type = str_replace('Filter', '', $type);
            $type = $type . 'Filter';
        }

        if (! in_array($type, Filter::TYPES, true)) {
            throw new InvalidFilterType($type);
        }

        $this->type = $type;

        if (! is_int($labelOrIndex) && ! is_string($labelOrIndex)) {
            throw new InvalidArgumentException($labelOrIndex, 'string | int');
        }

        if (is_int($labelOrIndex)) {
            $this->setColumnIndex($labelOrIndex);
        }

        if (is_string($labelOrIndex)) {
            $this->setColumnLabel($labelOrIndex);
        }
    }

    /**
     * Returns the type of Filter
     *
     * @return string
     */
    public function getType()
    {
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

    /**
     * Convert the Filter to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type'     => $this->getType(),
            'wrapType' => $this->getWrapType(),
            'options'  => $this->options->toArray(),
        ];
    }

    /**
     * Set the Column index to be filtered.
     *
     * If label was set, it will be replaced by the index.
     *
     * @since 4.0.0
     * @param int $index
     */
    public function setColumnIndex($index)
    {
        if ($this->options->has('filterColumnLabel')) {
            $this->options->forget('filterColumnLabel');
        }

        $this->options->set('filterColumnIndex', $index);
    }

    /**
     * Set the Column label to be filtered.
     *
     * If index was set, it will be replaced by the label.
     *
     * @since 4.0.0
     * @param int $label
     */
    public function setColumnLabel($label)
    {
        if ($this->options->has('filterColumnIndex')) {
            $this->options->forget('filterColumnIndex');
        }

        $this->options->set('filterColumnLabel', $label);
    }

    /**
     * Create a new ControlWrapper using the Filter.
     *
     * @param string $elementId
     * @return ControlWrapper
     */
    public function getControlWrapper($elementId)
    {
        $elementId = Str::verify($elementId);

        return new ControlWrapper($this, $elementId);
    }
}
