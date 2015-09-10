<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * CssClassNames Properties Object
 *
 * An object containing all the values for the cssClassNames which can be
 * passed into the Table's options.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @author     Peter Draznik <peter.draznik@38thStreetStudios.com>
 * @since	   3.0.0
 * @copyright  (c) 2015, 38th Street Studios
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class CssClassNames extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'CssClassNames';

    /**
     * Default options for Gradient
     *
     * @var array
     */
    private $defaults = [
        'headerRow',
        'tableRow',
        'oddTableRow',
        'selectedTableRow',
        'hoverTableRow',
        'headerCell',
        'tableCell',
        'rowNumberCell'
    ];

    /**
     * Builds the CssClassNames object when passed an array of options.
     *
     * @param  array $config Options for the CssClassNames
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Assigns a class name to the table header row (<tr> element).
     *
     * @access public
     * @param  string $headerRow
     * @return \Khill\Lavacharts\Configs\CssClassNames
     */
    public function headerRow($headerRow)
    {
         return $this->setStringOption($headerRow);
    }


    /**
     * Assigns a class name to the non-header rows (<tr> elements).
     *
     * @access public
     * @param  string $tableRow
     * @return \Khill\Lavacharts\Configs\CssClassNames
     */
    public function tableRow($tableRow)
    {
         return $this->setStringOption($tableRow);
    }


    /**
     * Assigns a class name to odd table rows (<tr> elements). Note: the alternatingRowStyle option must be set to true.
     *
     * @access public
     * @param  string $oddTableRow
     * @return \Khill\Lavacharts\Configs\CssClassNames
     */
    public function oddTableRow($oddTableRow)
    {
         return $this->setStringOption($oddTableRow);
    }


    /**
     * Assigns a class name to the selected table row (<tr> element).
     *
     * @access public
     * @param  string $selectedTableRow
     * @return \Khill\Lavacharts\Configs\CssClassNames
     */
    public function selectedTableRow($selectedTableRow)
    {
         return $this->setStringOption($selectedTableRow);
    }


    /**
     * Assigns a class name to the hovered table row (<tr> element).
     *
     * @access public
     * @param  string $hoverTableRow
     * @return \Khill\Lavacharts\Configs\CssClassNames
     */
    public function hoverTableRow($hoverTableRow)
    {
         return $this->setStringOption($hoverTableRow);
    }


    /**
     * Assigns a class name to all cells in the header row (<td> element).
     *
     * @access public
     * @param  string $headerCell
     * @return \Khill\Lavacharts\Configs\CssClassNames
     */
    public function headerCell($headerCell)
    {
         return $this->setStringOption($headerCell);
    }


    /**
     * Assigns a class name to all non-header table cells (<td> element).
     *
     * @access public
     * @param  string $tableCell
     * @return \Khill\Lavacharts\Configs\CssClassNames
     */
    public function tableCell($tableCell)
    {
         return $this->setStringOption($tableCell);
    }


    /**
     * Assigns a class name to the cells in the row number column (<td> element).
     *
     * @access public
     * @param  string $rowNumberCell
     * @return \Khill\Lavacharts\Configs\CssClassNames
     */
    public function rowNumberCell($rowNumberCell)
    {
         return $this->setStringOption($rowNumberCell);
    }
}
