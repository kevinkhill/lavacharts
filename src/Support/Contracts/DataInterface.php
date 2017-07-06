<?php

namespace Khill\Lavacharts\Support\Contracts;

/**
 * DataTable Interface
 *
 * Provides a standard for converting an object to a javascript DataTable.
 *
 * Example:
 *   The standard DataTable class which will use this method to convert
 *   the DataTable into JSON and then simply create a new DataTable in javascript
 *   with "new google.visualization.DataTable(%s)"
 *
 * Another Example:
 *   This was pulled straight from Google's SteppedAreaChart) would be to
 *   create the array of data however you wish, and from this method, output:
 *
 *   google.visualization.arrayToDataTable([
 *       ['Director (Year)',  'Rotten Tomatoes', 'IMDB'],
 *       ['Alfred Hitchcock (1935)', 8.4,         7.9],
 *       ['Ralph Thomas (1959)',     6.9,         6.5],
 *       ['Don Sharp (1978)',        6.5,         6.4],
 *       ['James Hawes (2008)',      4.4,         6.2]
 *   ]);
 *
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface DataInterface
{
    /**
     * Return the DataTable as the javascript representation.
     *
     * This method must return an string representation of a "DataTable" as
     * defined by the javascript object google.visualization.DataTable
     *
     *
     * @link https://developers.google.com/chart/interactive/docs/reference#datatable-class Google DataTable API
     * @return string Must be a representation of the javascript object google.visualization.DataTable
     */
    public function toJsDataTable();
}
