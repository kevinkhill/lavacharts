<?php

namespace Khill\Lavacharts\Datatables\Columns;

//use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Datatables\Datatable;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;
use \Khill\Lavacharts\Exceptions\InvalidColumnType;

class ColumnFactory
{
    private static $columnTypes = [
        'RoleColumn',
        'StringColumn',
        'NumberColumn',
        'BooleanColumn',
        'DateColumn',
        'DateTimeColumn',
        'TimeOfDayColumn'
    ];

    public static function __callStatic($type, $parameters)
    {
        if (in_array($type, self::$columnTypes) === false) {
            throw new InvalidColumnType($type, self::$columnTypes);
        }

        array_unshift($parameters, $type); //prepend

        return forward_static_call_array('self::create', $parameters);
    }

    private static function create($type, $label, $format, $role)
    {
        $columnType  = __NAMESPACE__ . '\\' . $type;

        $label  = new Label($label);
        $column = new $columnType($label);

        if ($format instanceof Format) {
            $column->setFormat($format);
        }

        if ($role instanceof ColumnRole) {
            $column->setRole($role);
        }

        return $column;
    }
}
