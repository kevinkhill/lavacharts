<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;

//@TODO: phpdocs!
class ChartBuilder
{
    private $type = null;
    private $label = null;
    private $datatable = null;
    private $options = null;
    private $elementId = null;

    public function setType($type)
    {
        if (in_array($type, ChartFactory::$CHART_TYPES) === false) {
            throw new InvalidChartType($type);
        }

        $this->type = $type;

        return $this;
    }

    public function setLabel($label)
    {
        $this->label = new Label($label);

        return $this;
    }

    public function setDatatable(DataTable $datatable)
    {
        $this->datatable = $datatable;

        return $this;
    }

    public function setOptions($options)
    {
        $this->options = new Options($options);

        return $this;
    }

    public function setElementId($elementId)
    {
        $this->elementId = new ElementId($elementId);

        return $this;
    }

    public function getChart()
    {
        $chart = __NAMESPACE__ . '\\' . $this->type;

        return new $chart(
            $this->label,
            $this->datatable,
            $this->options,
            $this->elementId
        );
    }
}
