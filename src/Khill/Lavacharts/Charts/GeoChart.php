<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * GeoChart Class
 *
 * A Geochart is a map of a country, a continent, or a region with two modes:
 * - The region mode colorizes whole regions, such as countries, provinces,
 *   or states.
 * - The marker mode marks designated regions using bubbles that are scaled
 *   according to a value that you specify.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Charts\Chart;

class GeoChart extends Chart
{
    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge($this->defaults, array(
            'colorAxis',
            'datalessRegionColor',
            'displayMode',
            'enableRegionInteractivity',
            'keepAspectRatio',
            'region',
            'magnifyingGlass',
            'markerOpacity',
            'resolution',
            'sizeAxis'
        ));
    }

    /**
     * An object that specifies a mapping between color column values and colors
     * or a gradient scale.
     *
     * @param colorAxis $colorAxis
     * @return \GeoChart
     */
    public function colorAxis($colorAxis)
    {
        if(is_a($colorAxis, 'colorAxis'))
        {
            $this->addOption($colorAxis);
        } else {
            $this->type_error(__FUNCTION__, 'colorAxis');
        }

        return $this;
    }

    /**
     * Color to assign to regions with no associated data.
     *
     * @param string $datalessRegionColor
     * @return \GeoChart
     */
    public function datalessRegionColor($datalessRegionColor)
    {
        if(is_string($datalessRegionColor) && ! empty($datalessRegionColor))
        {
            $this->addOption(array('datalessRegionColor' => $datalessRegionColor));
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Which type of map this is. The DataTable format must match the value specified. The following values are supported:
     *
     * 'auto' - Choose based on the format of the DataTable.
     * 'regions' - This is a region map
     * 'markers' - This is a marker map
     *
     * @param string $displayMode
     * @return \GeoChart
     */
    public function displayMode($displayMode)
    {
        $values = array(
            'auto',
            'regions',
            'markers',
        );

        if(in_array($displayMode, $values))
        {
            $this->addOption(array('displayMode' => $displayMode));
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * If true, enable region interactivity, including focus and tool-tip
     * elaboration on mouse hover, and region selection and firing of
     * regionClick and select events on mouse click.
     *
     * The default is true in region mode, and false in marker mode.
     *
     * @param type $enableRegionInteractivity
     * @return \GeoChart
     */
    public function enableRegionInteractivity($enableRegionInteractivity)
    {
        if(is_bool($enableRegionInteractivity))
        {
            $this->addOption(array('enableRegionInteractivity' => $enableRegionInteractivity));
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * If true, the map will be drawn at the largest size that can fit inside
     * the chart area at its natural aspect ratio. If only one of the width
     * and height options is specified, the other one will be calculated
     * according to the aspect ratio.
     *
     * If false, the map will be stretched to the exact size of the chart as
     * specified by the width and height options.
     *
     * @param boolean $keepAspectRatio
     * @return \GeoChart
     */
    public function keepAspectRatio($keepAspectRatio)
    {
        if(is_bool($keepAspectRatio))
        {
            $this->addOption(array('keepAspectRatio' => $keepAspectRatio));
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * The area to display on the map. (Surrounding areas will be displayed
     * as well.) Can be one of the following:
     *
     * 'world' - A map of the entire world.
     * A continent or a sub-continent, specified by its 3-digit code, e.g., '011' for Western Africa.
     * A country, specified by its ISO 3166-1 alpha-2 code, e.g., 'AU' for Australia.
     * A state in the United States, specified by its ISO 3166-2:US code, e.g., 'US-AL' for Alabama. Note that the resolution option must be set to either 'provinces' or 'metros'.
     *
     * @param string $region
     * @return \GeoChart
     */
    public function region($region)
    {
        if(is_string($region))
        {
            $this->addOption(array('region' => $region));
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * Sets up the magnifying glass, so when the user lingers over a cluttered
     * marker, a magnifiying glass will be opened.
     *
     * @param magnifyingGlass $magnifyingGlass
     * @return \GeoChart
     */
    public function magnifyingGlass($magnifyingGlass)
    {
        if(is_a($magnifyingGlass, 'magnifyingGlass'))
        {
            $this->addOption($magnifyingGlass);
        } else {
            $this->type_error(__FUNCTION__, 'object', 'of class magnifyingGlass');
        }

        return $this;
    }

    /**
     * The opacity of the markers, where 0.0 is fully transparent and 1.0
     * is fully opaque.
     *
     * @param type $markerOpacity
     * @return \GeoChart
     */
    public function markerOpacity($markerOpacity)
    {
        if(is_float($markerOpacity) && between($markerOpacity, 0, 1))
        {
            $this->addOption(array('markerOpacity' => $markerOpacity));
        } else {
            $this->type_error(__FUNCTION__, 'float', 'between 0.0 - 1.0');
        }

        return $this;
    }

    /**
     * The resolution of the map borders. Choose one of the following values:
     *
     * 'countries' - Supported for all regions, except for US state regions.
     * 'provinces' - Supported only for country regions and US state regions.
     *               Not supported for all countries; please test a country to
     *               see whether this option is supported.
     * 'metros' - Supported for the US country region and US state regions only.
     *
     * @param string $resolution
     * @return \GeoChart
     */
    public function resolution($resolution)
    {
        $values = array(
            'countries',
            'provinces',
            'metros',
        );

        if(in_array($resolution, $values))
        {
            $this->addOption(array('resolution' => $resolution));
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * An object with members to configure how values are associated with
     * bubble sizes.
     *
     * @param sizeAxis $sizeAxis
     * @return \GeoChart
     */
    public function sizeAxis($sizeAxis)
    {
        if(is_a($sizeAxis, 'sizeAxis'))
        {
            $this->addOption($sizeAxis);
        } else {
            $this->type_error(__FUNCTION__, 'object', 'of class sizeAxis');
        }

        return $this;
    }

}
