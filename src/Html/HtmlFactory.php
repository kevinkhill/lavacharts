<?php

namespace Khill\Lavacharts\Html;

use \Khill\Lavacharts\Exceptions\InvalidConfigValue;
use \Khill\Lavacharts\Exceptions\InvalidDivDimensions;

//@TODO: phpdocs
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
     * @since  1.0.0
     * @param  string               $elementId  Element id to apply to the div.
     * @param  array                $dimensions Height & width of the div.
     * @throws \Khill\Lavacharts\Exceptions\InvalidDivDimensions
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return string               HTML div element.
     */
    public function createDiv($elementId, $dimensions = true)
    {
        if ($dimensions === true) {
            return sprintf('<div id="%s"></div>', $elementId);
        } else {
            if (is_array($dimensions) && ! empty($dimensions)) {

                $widthStr = '';
                $heightStr = '';

                if (array_key_exists('height', $dimensions)) {
                    $heightType = $this->dimensionTypeCheck($dimensions['height']);
                    $heightStr = ($heightType === 'integer') ? sprintf("height:%spx;", $dimensions['height']) : sprintf("height:%s;", $dimensions['height']);
                }

                if (array_key_exists('width', $dimensions)) {
                    $widthType = $this->dimensionTypeCheck($dimensions['width']);
                    $widthStr = ($widthType === 'integer') ? sprintf("width:%spx;", $dimensions['width']) : sprintf("width:%s;", $dimensions['width']);
                }

                return sprintf(
                            '<div id="%s" style="%s%s"></div>',
                            $elementId,
                            $heightStr,
                            $widthStr
                        );

            } else {
                throw new InvalidDivDimensions();
            }
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
    private function dimensionTypeCheck($dimension)
    {
        if (is_int($dimension) && $dimension > 0) {
            return 'integer';
        } else if (substr($dimension, -1) === '%' && (int) substr($dimension, 0, -1) > 0) {
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
