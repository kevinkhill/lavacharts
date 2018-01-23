<?php

namespace Khill\Lavacharts\Support;

use Carbon\Carbon;
use DateTime;
use JsonSerializable;
use Khill\Lavacharts\Support\Traits\DateableTrait;

class Date implements JsonSerializable
{
    use DateableTrait;

    /**
     * @var Carbon
     */
    private $datetime;

    /**
     * JavascriptDate constructor.
     *
     * @param DateTime $datetime
     */
    public function __construct(DateTime $datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * Serialize the Carbon instance to it's javascript representation.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toJsDate($this->datetime);
    }
}
