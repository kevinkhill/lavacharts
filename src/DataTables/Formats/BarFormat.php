<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Support\Google;

/**
 * Class BarFormat
 *
 * Adds a colored bar to a numeric cell indicating whether the cell value
 * is above or below a specified base value.
 *
 *
 * @inheritDoc
 * @see https://developers.google.com/chart/interactive/docs/reference#barformatter
 */
class BarFormat extends Format
{
    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return Google::STANDARD_NAMESPACE . 'BarFormat';
    }
}
