<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\DataTables\Columns\Column;
use Khill\Lavacharts\Exceptions\InvalidFormatType;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\Javascriptable;
use Khill\Lavacharts\Support\Contracts\JsClass;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;

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
abstract class Format implements Customizable, Javascriptable, JsClass
{
    use HasOptions, ToJavascript;

    /**
     * Index of the Column that is formatted
     *
     * @var int
     */
    private $index;

    /**
     * Returns the javascript namespaced format class
     *
     * @return string
     */
    abstract public function getJsClass();

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
     * Return a format string that will be used by vsprintf to convert the
     * extending class to javascript.
     *
     * @return string
     */
    public function getJavascriptFormat()
    {
        /**
         * In the scope of the formats, "this" is a reference to the lavachart class.
         */
        return  <<<'FORMAT'
            this.formats['col-%1$s'] = new %2$s(%3$s);
            this.formats['col-%1$s'].format(this.data, %1$s);
FORMAT;
    }

    /**
     * Return an array of arguments to pass to the format string provided
     * by getJavascriptFormat().
     *
     * These variables will be used with vsprintf, and the format string
     * to convert the extending class to javascript.
     *
     * @return array
     */
    public function getJavascriptSource()
    {
        return [
            $this->index,
            $this->getJsClass(),
            $this->options
        ];
    }
}
