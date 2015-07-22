<?php

namespace Khill\Lavacharts\Datatables\Columns;

//use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;
use \Khill\Lavacharts\Exceptions\InvalidColumnType;

class ColumnFactory
{
    private static $columnTypes = [
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

    private static function create($type, $label, $id, $format, $role)
    {
        $label = new Label($label);
        $id    = new Label($id);
        $type  = __NAMESPACE__ . '\\' . $type;

        $column = new $type($label, $id);

        if (is_null($format) === false) {
            $column->setFormat($format);
        }

        if (is_null($role) === false) {
            $column->setRole($role);
        }

        return $column;
    }

}
