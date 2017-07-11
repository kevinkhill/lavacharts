<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Support\Google;

/**
 * Class ArrowFormat
 *
 * Adds an up or down arrow to a numeric cell, depending on whether the value
 * is above or below a specified base value. If equal to the base value, no arrow is shown.
 *
 *
 * @inheritDoc
 * @see https://developers.google.com/chart/interactive/docs/reference#arrowformatter
 */
class ArrowFormat extends Format
{
    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return self::GOOGLE_VISUALIZATION . 'ArrowFormat';
    }
}
