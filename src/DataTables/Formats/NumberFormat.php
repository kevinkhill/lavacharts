<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Support\Contracts\JsClass;

/**
 * NumberFormat Class
 *
 * Formats number values in the datatable for display.
 * Added to columns during column definition.
 *
 *
 * @inheritDoc
 * @see https://developers.google.com/chart/interactive/docs/reference#numberformatter
 */
class NumberFormat extends Format implements JsClass
{
    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return self::GOOGLE_VIZ . 'NumberFormat';
    }
}
