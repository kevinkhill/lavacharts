<?php

namespace Khill\Lavacharts\Support\Html;

use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidDivDimensions;

/**
 * Temporary class until removal in 3.2
 */
class HtmlFactory
{
    /**
     * Builds a div html element for the chart to be rendered into.
     *
     * Calling with no arguments will return a div with the ID set to what was
     * given to the outputInto() function.
     *
     * Passing two (int)s will set the width and height respectively and the div
     * ID will be set via the string given in the outputInto() function.
     *
     *
     * This is useful for the AnnotatedTimeLine Chart since it MUST have explicitly
     * defined dimensions of the div it is rendered into.
     *
     * The other charts do not require height and width, but do require an ID of
     * the div that will be receiving the chart.
     *
     * @access private
     * @since  3.1.0
     * @param  string     $elementId  Element id to apply to the div.
     * @param  array|bool $dimensions Height & width of the div.
     * @throws \Khill\Lavacharts\Exceptions\InvalidDivDimensions
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return string HTML div element.
     */
    public static function createDiv($elementId, $dimensions = true)
    {
        if ($dimensions === true) {
            return sprintf('<div id="%s"></div>', $elementId);
        } else {
            if (is_array($dimensions) === false || empty($dimensions)) {
                throw new InvalidDivDimensions();
            }

            $widthStr = '';
            $heightStr = '';

            if (array_key_exists('height', $dimensions)) {
                $heightType = self::dimensionTypeCheck($dimensions['height']);
                $heightUnit = ($heightType === 'integer') ? 'px' : '';
                $heightStr  = sprintf("height:%s%s;", $dimensions['height'], $heightUnit);
            }

            if (array_key_exists('width', $dimensions)) {
                $widthType = self::dimensionTypeCheck($dimensions['width']);
                $widthUnit = ($widthType === 'integer') ? 'px' : '';
                $widthStr = sprintf("width:%s%s;", $dimensions['width'], $widthUnit);
            }

            return sprintf(
                '<div id="%s" style="%s%s"></div>',
                $elementId,
                $heightStr,
                $widthStr
            );
        }
    }

    /**
     * Returns whether a given dimension value is an integer,
     * percentage, or invalid.
     *
     * @access private
     * @since  3.0.0
     * @param  int|string $dimension An integer or a string representing a percent.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return string
     */
    private static function dimensionTypeCheck($dimension)
    {
        if (is_int($dimension) && $dimension > 0) {
            return 'integer';
        } elseif (substr($dimension, -1) === '%' && (int) substr($dimension, 0, -1) > 0) {
            return 'percentage';
        } else {
            throw new InvalidConfigValue(
                __METHOD__,
                'int|%',
                'greater than 0'
            );
        }
    }
}
