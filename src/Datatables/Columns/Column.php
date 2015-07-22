<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class Column implements \JsonSerializable
{
    protected $label;

    protected $id;

    protected $format;

    protected $role;

    public function __construct(Label $label)
    {
        $this->label = $label;

        $this->setIdFromLabel();
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

    private function setIdFromLabel()
    {
        $id = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', (string) $this->label);
        $id = strtolower(trim($id, '-'));

        $this->id = preg_replace("/[\/_|+ -]+/", '-', $id);
    }
}

