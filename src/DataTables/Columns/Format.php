<?php

namespace Khill\Lavacharts\DataTables\Columns;

use JsonSerializable;
use Khill\Lavacharts\Exceptions\InvalidFormatType;
use Khill\Lavacharts\Support\Contracts\Customizable;
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
    use HasOptions;

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
     * Format constructor.
     *
     * @param string $type
     * @param array  $options
     * @throws InvalidFormatType
     */
    public function __construct($type, array $options = [])
    {
        if (! in_array($type, static::TYPES)) {
            throw new InvalidFormatType($type);
        }

        $this->type = $type;

        $this->setOptions($options);
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

    public function toJson()
    {
        return json_encode($this);
    }

    public function toArray()
    {
        return [
            'type'    => $this->type,
            'index'   => $this->index,
            'options' => $this->options
        ];
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
