<?php namespace Khill\Lavacharts\Charts;

/**
 * Table Chart Class
 *
 * A table chart is rendered within the browser. Displays a data from a DataTable in an easily sortable form.
 * Can be searched by rendering as a wrapper and binding to a control within a dashboard.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      v3.0.0
 * @author     Peter Draznik <peter.draznik@38thStreetStudios.com>
 * @copyright  (c) 2015, 38th Street Studios
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Configs\CssClassNames;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Options;
use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Values\Label;

class TableChart extends Chart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'TableChart';

    /**
     * Javascript chart version.
     *
     * @var string
     */
    const VERSION = '1';

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'table';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Table';

    /**
     * Default options for the TableChart
     * @var array
     */
    private $tableDefaults = [
        'allowHtml',
        'alternatingRowStyle',
        'cssClassNames',
        'firstRowNumber',
        'frozenColumns',
        'page',
        'pageSize',
        'pagingButtons',
        'rtlTable',
        'scrollLeftStartPosition',
        'showRowNumber',
        'sortTable',
        'sortAscending',
        'sortColumn',
        'startPage',
    ];

    /**
     * Builds a new TableChart with the given label, datatable and options.
     *
     * @param  \Khill\Lavacharts\Values\Label $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable                       $datatable DataTable used for the chart.
     * @param  array                                                        $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->tableDefaults);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }

    /**
     * If set to true the GoogleChart will render html tags sorted as values.
     *
     * @access public
     * @param  bool $html
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function allowHtml($html)
    {
        return $this->setBoolOption($html);
    }

    /**
     * If set to true the GoogleChart will alternate the role styles.
     *
     * @access public
     * @param  bool $astyle
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function alternatingRowStyle($astyle)
    {
        return $this->setBoolOption($astyle);
    }


    /**
     * An object in which each property name describes a table element, and the property value is a string,
     * defining a class to assign to that table element.
     *
     * Use this property to assign custom CSS to specific elements of your table.
     * To use this property, assign an object, where the property name specifies the
     * table element, and the property value is a string, specifying a class name to assign to that element.
     * You must then define a CSS style for that class on your page. The following property names are supported:
     *  headerRow - Assigns a class name to the table header row (<tr> element).
     *  tableRow - Assigns a class name to the non-header rows (<tr> elements).
     *  oddTableRow - Assigns a class name to odd table rows (<tr> elements).
     *    Note: the alternatingRowStyle option must be set to true.
     *  selectedTableRow - Assigns a class name to the selected table row (<tr> element).
     *  hoverTableRow - Assigns a class name to the hovered table row (<tr> element).
     *  headerCell - Assigns a class name to all cells in the header row (<td> element).
     *  tableCell - Assigns a class name to all non-header table cells (<td> element).
     *  rowNumberCell - Assigns a class name to the cells in the row number column (<td> element).
     *    Note: the showRowNumber option must be set to true.
     *
     *  Example: var cssClassNames = {headerRow: 'bigAndBoldClass', hoverTableRow: 'highlightClass'};
     *
     * @access public
     * @param  array $classNameConfig
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     *
     */
    public function cssClassNames($classNameConfig)
    {
        return $this->addOption(__FUNCTION__, new CssClassNames($classNameConfig));
    }


    /**
     * The row number for the first row in the dataTable. Used only if showRowNumber is true.
     *
     * @access public
     * @param  int $firstRowNumber
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function firstRowNumber($firstRowNumber)
    {
        return $this>$this->setIntOption($firstRowNumber);
    }


    /**
     * The number of columns from the left that will be frozen.
     *
     * These columns will remain in place when scrolling the remaining columns horizontally.
     * If showRowNumber is false, setting frozenColumns to 0 will appear the same as if set to null,
     * but if showRowNumber is set to true, the row number column will be frozen.
     *
     * @access public
     * @param  int $frozenColumns
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function frozenColumns($frozenColumns)
    {
        return $this->setIntOption($frozenColumns);
    }


    /**
     * If and how to enable paging through the data.
     *
     * Choose one of the following string values:
     *	'enable' - The table will include page-forward and page-back buttons. Clicking on these buttons will
     *		 perform the paging operation and change the displayed page. You might want to also set the pageSize option.
     *	'event' - The table will include page-forward and page-back buttons, but clicking them will trigger a 'page'
     *		 event and will not change the displayed page. This option should be used when the code implements its own
     *		 page turning logic. See the TableQueryWrapper example for an example of how to handle paging events manually.
     *	'disable' - [Default] Paging is not supported.
     *
     * @access public
     * @param  string $page
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function page($page)
    {
        $values = array(
            'enable',
            'event',
            'disable',
        );

        if (Utils::nonEmptyStringInArray($page, $values)||is_int($page)) {
            $this->addOption(array(__FUNCTION__ => $page));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }


    /**
     * The number of rows in each page, when paging is enabled with the page option.
     *
     * @access public
     * @param  int $pageSize
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pageSize($pageSize)
    {
        return $this->setIntOption($pageSize);
    }


    /**
     * Sets a specified option for the paging buttons. The options are as follows:
     *  both - enable prev and next buttons
     *  prev - only prev button is enabled
     *  next - only next button is enabled
     *  auto - the buttons are enabled according to the current page. On the first page only next
     *         is shown. On the last page only prev is shown. Otherwise both are enabled.
     *  number - the number of paging buttons to show. This explicit number will override computed number from pageSize.
     *
     * @access public
     * @param  string|int $paging
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pagingButtons($paging)
    {
        $values = [
            'both',
            'prev',
            'next',
            'auto'
        ];

        if (Utils::nonEmptyStringInArray($paging, $values) === false || is_int($paging) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string|int',
                'must be int or one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->setOption(__FUNCTION__, $paging);
    }


    /**
     * Adds basic support for right-to-left languages (such as Arabic or Hebrew) by reversing the column
     * order of the table, so that column zero is the rightmost column, and the last column is the leftmost
     * column. This does not affect the column index in the underlying data, only the order of display. Full
     * bi-directional (BiDi) language display is not supported by the table visualization even with this
     * option. This option will be ignored if you enable paging (using the page option), or if the table has
     * scroll bars because you have specified height and width options smaller than the required table size.
     *
     * @access public
     * @param  bool $rtl
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function rtlTable($rtl)
    {
        return $this->setBoolOption($rtl);
    }

    /**
     * Sets the horizontal scrolling position, in pixels, if the table has horizontal scroll bars because you
     * have set the width property. The table will open scrolled that many pixels past the leftmost column.
     *
     * @access public
     * @param  int $startPosition
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function scrollLeftStartPosition($startPosition)
    {
        return $this->setIntOption($startPosition);
    }

    /**
     * If set to true, shows the row number as the first column of the table.
     *
     * @access public
     * @param  bool $rowNumber
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function showRowNumber($rowNumber)
    {
        return $this->setBoolOption($rowNumber);
    }

    /**
     * If and how to sort columns when the user clicks a column heading. If sorting is enabled, consider setting
     * the sortAscending and sortColumn properties as well. Choose one of the following string values:
     *  'enable' - [Default] Users can click on column headers to sort by the clicked column. When users click
     *             on the column header, the rows will be automatically sorted, and a 'sort' event will be triggered.
     *  'event' - When users click on the column header, a 'sort' event will be triggered, but the rows will not be
     *            automatically sorted. This option should be used when the page implements its own sort. See the
     *            TableQueryWrapper example for an example of how to handle sorting events manually.
     *  'disable' - Clicking a column header has no effect.
     *
     * @access public
     * @param  string $sort
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function sortTable($sort)
    {
        $values = [
            'enable',
            'event',
            'disable'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $sort, $values);
    }

    /**
     * The order in which the initial sort column is sorted. True for ascending, false for
     * descending. Ignored if sortColumn is not specified.
     *
     * @access public
     * @param  bool $sort
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function sortAscending($sort)
    {
        return $this->setBoolOption($sort);
    }

    /**
     * An index of a column in the data table, by which the table is initially sorted.
     * The column will be marked with a small arrow indicating the sort order.
     *
     * @access public
     * @param  int $sort
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function sortColumn($sort)
    {
        return $this->setIntOption($sort);
    }

    /**
     * The first table page to display. Used only if page is in mode enable/event.
     *
     * @access public
     * @param  int $start
     * @return \Khill\Lavacharts\Charts\TableChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function startPage($start)
    {
        return $this->setIntOption($start);
    }
}
