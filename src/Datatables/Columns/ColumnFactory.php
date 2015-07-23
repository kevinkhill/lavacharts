<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Datatables\Formats\Format;
use \Khill\Lavacharts\Datatables\Columns\ColumnRole;
use \Khill\Lavacharts\Exceptions\InvalidColumnType;

class ColumnFactory
{
    private static $columnTypes = [
        'role',
        'string',
        'number',
        'boolean',
        'date',
        'datetime',
        'timeofday'
    ];

    public static function create($type, $label='', $format=null, $role='')
    {
        if (Utils::nonEmptyStringInArray($type, self::$columnTypes) === false) {
            throw new InvalidColumnType($type, self::$columnTypes);
        }

        $columnType = __NAMESPACE__ . '\\' . ucfirst($type) . 'Column';

        $column = new $columnType($label);

        if ($format instanceof Format) {
            $column->setFormat($format);
        }
        var_dump($role);
        if (Utils::nonEmptyString($role) === true) {
            $role = new ColumnRole($role);

            $column->setRole($role);
        }

        return $column;
    }

    private static function createWithRole($role)
    {

    }
}
