<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Role;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

abstract class Column implements \JsonSerializable
{
    protected $label;

    protected $id;

    protected $format;

    protected $role;

    public function __construct(Label $label, Label $id)
    {
        $this->label  = $label;
        $this->id     = $id;
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

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setRole($role)
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

        if (is_null($this->format) === false) {
            $values['format'] = $this->format;
        }

        if (is_null($this->role) === false) {
            $values['role'] = $this->role;
        }

        return $values;
    }
}

