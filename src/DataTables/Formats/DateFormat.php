<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Support\Contracts\JsClass;

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
class DateFormat extends Format implements JsClass
{
    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return self::GOOGLE_VIZ . 'DateFormat';
    }
}
