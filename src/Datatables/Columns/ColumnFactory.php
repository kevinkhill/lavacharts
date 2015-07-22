<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class ColumnFactory
{
    private static $columnTypes = [
        'string',
        'number',
        'boolean',
        'date',
        'datetime',
        'timeofday'
    ];

    public static function create($type, $label, $id, Format $format = null)
    {
        if (Utils::nonEmptyString($type) === false || in_array($type, self::columnTypes) === false) {
            throw new InvalidColumnType($type, self::columnTypes);
        }

        $label  = new Label($label);
        $id     = new Label($id);
        $column = __NAMESPACE__ . '\\' . ucfirst($type) . 'Column';

        return new $column($label, $id, $format);
    }
}
