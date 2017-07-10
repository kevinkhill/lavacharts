<?php

namespace Khill\Lavacharts\Charts;

/**
 * CalendarChart Class
 *
 * A calendar chart is a visualization used to show activity over the course of a long span of time,
 * such as months or years. They're best used when you want to illustrate how some quantity varies
 * depending on the day of the week, or how it trends over time.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     2.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class CalendarChart extends Chart
{
    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return '1.1';
    }

    /**
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return 'calendar';
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return self::GOOGLE_VISUALIZATION . ucfirst($this->getJsPackage());
    }
}
