<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\DataTables\Columns\Column;
use Khill\Lavacharts\Exceptions\InvalidFormatType;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\JsClass;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\Traits\CastsToJavascriptTrait as CastsToJavascript;
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
class Format implements Customizable, Javascriptable, Jsonable, JsClass
{
    use CastsToJavascript, HasOptions;

    /**
     * Format string for creating the events javascript
     */
    const FORMAT = <<<'FORMAT'
        this.formats['col-%1$s'] = new %2$s(%3$s);
        this.formats['col-%1$s'].format(this.data, %1$s);
FORMAT;

    /**
     * Index of the Column that is formatted
     *
     * @var int
     */
    private $index;

    /**
     * Format constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Static method for creating new format objects.
     *
     * @param  string $type
     * @param  array  $options
     * @return Format
     * @throws \Khill\Lavacharts\Exceptions\InvalidFormatType
     */
    public static function create($type, array $options = [])
    {
        $format = __NAMESPACE__ . '\\' . $type;

        if ( ! class_exists($format)) {
            throw new InvalidFormatType($type);
        }

        return new $format($options);
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
     * Returns the format type.
     *
     * @since 3.0.0
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * Javascript representation of the Format.
     *
     * @return string
     */
    public function getJsClass()
    {
        return 'google.visualization.' . static::TYPE;
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
     * Creates valid javascript from the instance.
     */
    public function toJavascript()
    {
        return sprintf(self::FORMAT, $this->type, $this->callback);
    }

    /**
     * JSON representation of the Format.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Return the options as the serialized Format.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->options->toArray();
    }
}
