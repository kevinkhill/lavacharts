<?php

namespace Khill\Lavacharts\DataTables\Columns;

use JsonSerializable;
use Khill\Lavacharts\Exceptions\InvalidFormatType;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;

/**
 * Class Format
 *
 * The base class for the individual format objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Khill\Lavacharts\DataTables\Formats
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @since      3.0.0
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class Format implements JsonSerializable, Customizable
{
    use HasOptions, ArrayToJson;

    const TYPES = [
        'ArrowFormat',
        'BarFormat',
        'DateFormat',
        'NumberFormat',
    ];

    /**
     * Index of the Column that is formatted
     *
     * @var int
     */
    private $index;

    /**
     * Type of format
     *
     * @var string
     */
    private $type;

    /**
     * Create a new Format by named type with an array of arguments.
     *
     * @param string $type
     * @param array  $args
     * @return Format
     */
    public static function create($type, $args)
    {
        return new static($type, count($args) > 0 ? $args[0] : []);
    }

    /**
     * Format constructor.
     *
     * @param string $type
     * @param array  $options
     * @throws InvalidFormatType
     */
    public function __construct($type, array $options = [])
    {
        if (is_string($type)) {
            $type = str_replace('Format', '', $type);
            $type = $type . 'Format';
        }

        if (! in_array($type, static::TYPES)) {
            throw new InvalidFormatType($type);
        }

        $this->type = $type;

        $this->setOptions($options);
    }

    /**
     * Convert the Format to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type'    => $this->type,
            'index'   => $this->index,
            'options' => $this->options
        ];
    }

    /**
     * Returns the type of format.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the index of the column that is formatted.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Sets the index of the formatted Column
     *
     * @param  int $index
     * @return self
     */
    public function setIndex($index)
    {
        $this->index = (int) $index;

        return $this;
    }

    /**
     * Apply the  a new Column with the same values, while applying the Format.
     *
     * @param  Column $column
     * @return Column
     */
    public function applyToColumn(Column $column)
    {
        return $column->setFormat($this);
    }
}
