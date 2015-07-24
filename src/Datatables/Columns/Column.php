<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class Column implements \JsonSerializable
{
    protected $label = '';

    protected $id = '';

    protected $format = null;

    protected $role = null;

    public function __construct($label='')
    {
        $this->label = $label;
    }

    public function getType()
    {
        return static::TYPE;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFormat(Format $format)
    {
        $this->format = $format;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setRole(ColumnRole $role)
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function jsonSerialize()
    {
        $values = [
            'type'  => static::TYPE,
            'label' => (string) $this->label,
            'id'    => (string) $this->id,
        ];
/*
        if ($this->format instanceof Format) {
            $values['f'] = $this->format;
        }
*/
        if ($this->role instanceof ColumnRole) {
            $values['p'] = $this->role;
        }

        return $values;
    }
}

