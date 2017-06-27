<?php

namespace Khill\Lavacharts\Javascript;

use \Khill\Lavacharts\Charts\Chart;

/**
 * ChartFactory Class
 *
 * This class takes Charts and uses all of the info to build the complete
 * javascript blocks for outputting into the page.
 *
 * @category   Class
 * @package    Khill\Lavacharts\Javascript
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class ChartJsFactory extends JavascriptFactory
{
    /**
     * Location of the output template.
     *
     * @var string
     */
    const OUTPUT_TEMPLATE = 'chart.tmpl.js';

    /**
     * Chart to create javascript from.
     *
     * @var \Khill\Lavacharts\Charts\Chart
     */
    protected $chart;

    /**
     * Event format template
     *
     * @var string
     */
    protected $eventTemplate;

    /**
     * Format format template
     *
     * @var string
     */
    protected $formatTemplate;

    /**
     * Creates a new ChartJsFactory with the javascript template.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to process
     */
    public function __construct(Chart $chart)
    {
        $this->chart = $chart;

        /**
         * In the scope of the events and formats, "this" is a reference to the lavachart class in question.
         */
        $this->formatTemplate =
            'this.formats["col%1$s"] = new %2$s(%3$s);'.PHP_EOL.
            'this.formats["col%1$s"].format(this.data, %1$s);'.PHP_EOL;

        $this->templateVars = $this->getTemplateVars();

        parent::__construct(self::OUTPUT_TEMPLATE);
    }

    /**
     * Builds the template variables from the chart.
     *
     * @since  3.0.0
     * @access protected
     * @return array
     */
    protected function getTemplateVars()
    {
        $vars = $this->chart->toArray();

        if (method_exists($this->chart, 'getPngOutput')) {
            $vars['pngOutput'] = $this->chart->getPngOutput();
        }

        if (
            method_exists($this->chart, 'getMaterialOutput') &&
            $this->chart->getMaterialOutput()
        ) {
            $vars['chartOptions'] = sprintf(
                $this->chart->getJsClass() . '.convertOptions(%s)',
                $this->chart->getOptions()->toJson()
            );
        }

        if ($this->chart->getDataTable()->hasFormattedColumns()) {
            $vars['formats'] = $this->buildFormatters();
        }

        return $vars;
    }

    /**
     * Builds the javascript for the datatable column formatters.
     *
     * @access protected
     * @return string Javascript code block.
     */
    protected function buildFormatters()
    {
        $buffer  = '';
        $columns = $this->chart->getDataTable()->getFormattedColumns();

        /**
         * @var int|string $index
         * @var \Khill\Lavacharts\DataTables\Columns\Column $column
         */
        foreach ($columns as $index => $column) {
            $format = $column->getFormat();

            $buffer .= sprintf(
                $this->formatTemplate,
                $index,
                $format->getJsClass(),
                $format->toJson()
            ).PHP_EOL;
        }

        return $buffer;
    }
}
