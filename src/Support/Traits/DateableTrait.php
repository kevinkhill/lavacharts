<?php

namespace Khill\Lavacharts\Support\Traits;

use Carbon\Carbon;
use DateTime;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Support\Traits\ElementIdTrait as HasElementId;

/**
 * Trait DateableTrait
 *
 * This trait will serialize a Date into the Javascript representation.
 *
 * @package    Khill\Lavacharts\Support
 * @since      3.1.10
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
trait DateableTrait
{
    /**
     * @param  \DateTime $datetime
     * @return string
     */
    function toJsDate(DateTime $datetime)
    {
        $c = Carbon::createFromFormat(DateTime::ATOM, $datetime->format(DateTime::ATOM));

        return sprintf(
            'Date(%d,%d,%d,%d,%d,%d)',
            isset($c->year)   ? $c->year      : 'null',
            isset($c->month)  ? $c->month - 1 : 'null', //silly javascript
            isset($c->day)    ? $c->day       : 'null',
            isset($c->hour)   ? $c->hour      : 'null',
            isset($c->minute) ? $c->minute    : 'null',
            isset($c->second) ? $c->second    : 'null'
        );
    }
}
