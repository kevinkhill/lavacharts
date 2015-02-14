<?php namespace Khill\Lavacharts\Configs;

/**
 * DataCell Object
 *
 * Holds the information for a data point
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * @codeCoverageIgnore
 */
class DataCell
{
    /**
     * The cell value.
     *
     * @var string
     */
    public $v;

    /**
     * A string version of the v value. (Optional)
     *
     * @var string
     */
    public $f;

    /**
     * An object that is a map of custom values applied to the cell. (Optional)
     *
     * @var string
     */
    public $p;

    /**
     * Defines a DataCell for a DataTable
     *
     * Each cell in the table holds a value. Cells can have a null value, or a
     * value of the type specified by its column. Cells optionally can take a
     * "formatted" version of the data; this is a string version of the data,
     * formatted for display by a visualization. A visualization can (but is
     * not required to) use the formatted version for display, but will always
     * use the data itself for any sorting or calculations that it makes (such
     * as determining where on a graph to place a point). An example might be
     * assigning the values "low" "medium", and "high" as formatted values to
     * numeric cell values of 1, 2, and 3.
     *
     * @see   DataTable
     * @param string $v The cell value
     * @param string $f A string version of the v value
     * @param string $p An object that is a map of custom values applied to the cell
     *
     * @return DataCell
     */
    public function __construct($v = null, $f = null, $p = null)
    {
        $this->v = $v;
        $this->f = $f;

        if (is_array($p)) {
            $vals = array();
            foreach ($p as $k => $v) {
                $vals[$k] = $v;
            }
            $this->p = $vals;
        } else {
            $this->p = $p;
        }

        return $this;
    }

    /**
     * Converts the DataCell object to JSON notation.
     *
     * @return string JSON string representation of the data cell
     */
    public function toJson()
    {
        $output = array();

        if ($this->v != null) {
            $output['v'] = $this->v;
        }
        if ($this->f != null) {
            $output['f'] = $this->f;
        }
        if ($this->p != null) {
            $output['p'] = $this->p;
        }

        return json_encode($output);
    }
}
