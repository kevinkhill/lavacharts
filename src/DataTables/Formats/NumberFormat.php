<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Support\Google;

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
class NumberFormat extends Format
{
    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return Google::VIZ_NAMESPACE . 'NumberFormat';
    }
}
