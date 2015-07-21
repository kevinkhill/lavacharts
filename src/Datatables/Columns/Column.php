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

    //TODO: add column roles

    public function __construct(Label $label, Label $id, Format $format = null, Role $role)
    {
        $this->label  = $label;
        $this->id     = $id;
        $this->format = $format;
        $this->role   = $role;
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

    public function getFormat()
    {
        return $this->format;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function jsonSerialize()
    {
        return [
            'type'  => static::TYPE,
            'label' => (string) $this->label,
            'id'    => (string) $this->id,
        ];
    }
}
