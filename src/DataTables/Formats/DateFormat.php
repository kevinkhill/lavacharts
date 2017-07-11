<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Support\Google;

/**
 * Class DateFormat
 *
 * Formats date values in the datatable for display.
 * Added to columns during column definition.
 *
 *
 * @inheritDoc
 * @see https://developers.google.com/chart/interactive/docs/reference#dateformatter
 */
class DateFormat extends Format
{
    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return self::GOOGLE_VISUALIZATION . 'DateFormat';
    }
}
