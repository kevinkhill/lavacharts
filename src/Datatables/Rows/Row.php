<?php

namespace Khill\Lavacharts\Datatables\Rows;

class Row implements \JsonSerializable
{
    private $values;

    public function __construct($valueArray)
    {
        $this->values = $valueArray;
    }

    public function jsonSerialize()
    {
        return [
            'c' => array_map(function ($cellValue) {
                return ['v' => $cellValue];
            }, $this->values)
        ];
    }
}
