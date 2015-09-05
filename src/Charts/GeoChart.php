<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Configs\SizeAxis;
use \Khill\Lavacharts\Configs\MagnifyingGlass;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

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
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class GeoChart extends Chart
{
    /**
     * Common methods
     */
    use \Khill\Lavacharts\Traits\ColorAxisTrait;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'GeoChart';

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
    const VIZ_PACKAGE = 'geochart';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.GeoChart';

    /**
     * Default configuration options for the chart.
     *
     * @var array
     */
    private $geoDefaults = [
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
    ];

    /**
     * Builds a new GeoChart with the given label, datatable and options.
     *
     * @param \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param array                                  $config
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->geoDefaults);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }

    /**
     * Color to assign to regions with no associated data.
     *
     * @param  string             $drc
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function datalessRegionColor($drc)
    {
        return $this->setStringOption(__FUNCTION__, $drc);
    }

    /**
     * Which type of map this is.
     *
     * The DataTable format must match the value specified.
     *
     * The following values are supported:
     * 'auto' - Choose based on the format of the DataTable.
     * 'regions' - This is a region map
     * 'markers' - This is a marker map
     *
     *
     * @param  string $displayMode
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function displayMode($displayMode)
    {
        $values = [
            'auto',
            'regions',
            'markers',
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $displayMode, $values);
    }

    /**
     * If true, enable region interactivity, including focus and tool-tip
     * elaboration on mouse hover, and region selection and firing of
     * regionClick and select events on mouse click.
     *
     * The default is true in region mode, and false in marker mode.
     *
     *
     * @param  bool $eri
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function enableRegionInteractivity($eri)
    {
        return $this->setBoolOption(__FUNCTION__, $eri);
    }

    /**
     * If true, the map will be drawn at the largest size that can fit inside
     * the chart area at its natural aspect ratio.
     *
     * If only one of the width
     * and height options is specified, the other one will be calculated
     * according to the aspect ratio.
     *
     * If false, the map will be stretched to the exact size of the chart as
     * specified by the width and height options.
     *
     *
     * @param  bool $kar
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function keepAspectRatio($kar)
    {
        return $this->setBoolOption(__FUNCTION__, $kar);
    }

    /**
     * The area to display on the map. (Surrounding areas will be displayed as well.)
     * Can be one of the following:
     *
     * 'world' - A map of the entire world.
     *
     * A continent or a sub-continent, specified by its 3-digit code, e.g., '011' for Western Africa.
     *
     * A country, specified by its ISO 3166-1 alpha-2 code, e.g., 'AU' for Australia.
     *
     * A state in the United States, specified by its ISO 3166-2:US code, e.g., 'US-AL' for Alabama.
     * Note that the resolution option must be set to either 'provinces' or 'metros'.
     *
     *
     * @param  string $region
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function region($region)
    {
        return $this->setStringOption(__FUNCTION__, $region);
    }

    /**
     * Sets up the magnifying glass, so when the user lingers over a cluttered
     * marker, a magnifiying glass will be opened.
     *
     * @param  array $magnifyingGlassConfig
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function magnifyingGlass($magnifyingGlassConfig)
    {
        return $this->setOption(__FUNCTION__, new MagnifyingGlass($magnifyingGlassConfig));
    }

    /**
     * The opacity of the markers, where 0.0 is fully transparent and 1.0
     * is fully opaque.
     *
     * @param  float $markerOpacity
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function markerOpacity($markerOpacity)
    {
        if (Utils::between(0, $markerOpacity, 1) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this->setOption(__FUNCTION__, $markerOpacity);
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
     * @param  string $resolution
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function resolution($resolution)
    {
        $values = [
            'countries',
            'provinces',
            'metros',
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $resolution, $values);
    }

    /**
     * An object with members to configure how values are associated with bubble sizes.
     *
     * @param  array $sizeAxisConfig
     * @return \Khill\Lavacharts\Charts\GeoChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function sizeAxis($sizeAxisConfig)
    {
        return $this->setOption(__FUNCTION__, new SizeAxis($sizeAxisConfig));
    }
}
