<?php namespace Khill\Lavacharts\Charts;

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
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Configs\ColorAxis;
use Khill\Lavacharts\Configs\SizeAxis;
use Khill\Lavacharts\Configs\MagnifyingGlass;
use Khill\Lavacharts\Utils;

class GeoChart extends Chart
{
    public $type = 'GeoChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge(
            $this->defaults,
            array(
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
            )
        );
    }

    /**
     * An object that specifies a mapping between color column values and colors
     * or a gradient scale.
     *
     * @uses   ColorAxis
     * @param  ColorAxis $ca
     * @return GeoChart
     */
    public function colorAxis(ColorAxis $ca)
    {
        $this->addOption($ca->toArray(__FUNCTION__));

        return $this;
    }

    /**
     * Color to assign to regions with no associated data.
     *
     * @param  string             $drc
     * @throws InvalidConfigValue
     * @return GeoChart
     */
    public function datalessRegionColor($drc)
    {
        if (is_string($drc) && ! empty($drc)) {
            $this->addOption(array(__FUNCTION__ => $drc));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
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
     * @param  string             $dm
     * @throws InvalidConfigValue
     * @return GeoChart
     */
    public function displayMode($dm)
    {
        $v = array(
            'auto',
            'regions',
            'markers',
        );

        if (is_string($dm) && in_array($dm, $v)) {
            $this->addOption(array(__FUNCTION__ => $dm));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($v)
            );
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
     * @param  bool               $eri
     * @throws InvalidConfigValue
     * @return GeoChart
     */
    public function enableRegionInteractivity($eri)
    {
        if (is_bool($eri)) {
            $this->addOption(array(__FUNCTION__ => $eri));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
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
     * @param  bool               $kar
     * @throws InvalidConfigValue
     * @return GeoChart
     */
    public function keepAspectRatio($kar)
    {
        if (is_bool($kar)) {
            $this->addOption(array(__FUNCTION__ => $kar));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
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
     * @param  string             $r
     * @throws InvalidConfigValue
     * @return GeoChart
     */
    public function region($r)
    {
        if (is_string($r)) {
            $this->addOption(array(__FUNCTION__ => $r));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets up the magnifying glass, so when the user lingers over a cluttered
     * marker, a magnifiying glass will be opened.
     *
     * @uses   MagnifyingGlass
     * @param  MagnifyingGlass $mg
     * @return GeoChart
     */
    public function magnifyingGlass(MagnifyingGlass $mg)
    {
        $this->addOption($mg->toArray(__FUNCTION__));

        return $this;
    }

    /**
     * The opacity of the markers, where 0.0 is fully transparent and 1.0
     * is fully opaque.
     *
     * @param  float|int          $mo
     * @throws InvalidConfigValue
     * @return GeoChart
     */
    public function markerOpacity($mo)
    {
        if ($mo === 0 || $mo === 1) {
            $this->addOption(array(__FUNCTION__ => $mo));
        } elseif (is_float($mo) && Utils::between(0.0, $mo, 1.0, true)) {
            $this->addOption(array(__FUNCTION__ => $mo));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float|int',
                'between 0 - 1'
            );
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
     * @param  string             $r
     * @throws InvalidConfigValue
     * @return GeoChart
     */
    public function resolution($r)
    {
        $v = array(
            'countries',
            'provinces',
            'metros',
        );

        if (is_string($r) && in_array($r, $v)) {
            $this->addOption(array(__FUNCTION__ => $r));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($v)
            );
        }

        return $this;
    }

    /**
     * An object with members to configure how values are associated with
     * bubble sizes.
     *
     * @uses   Sizeaxis
     * @param  SizeAxis $sa
     * @return GeoChart
     */
    public function sizeAxis(SizeAxis $sa)
    {
        $this->addOption($sa->toArray(__FUNCTION__));

        return $this;
    }
}
