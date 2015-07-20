<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

abstract class DataColumn implements \JsonSerializable
{
    protected $label;

    protected $id;

    protected $format;

    public function __construct(Label $label, Label $id)
    {
        $this->label  = $label;
        $this->id     = $id;
    }

    public function addFormat(Format $format)
    {
        $this->format = $format;
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

}
