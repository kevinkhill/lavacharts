<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Dashboards\Wrappers\Wrapper;
use Khill\Lavacharts\Exceptions\RenderingException;
use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Support\Buffer;
use Khill\Lavacharts\Values\ElementId;

/**
 * DashboardFactory Class
 *
 * This class takes Chart and Control Wrappers and uses all of the info to build the complete
 * javascript blocks for outputting into the page.
 *
 * @category   Class
 * @package    Khill\Lavacharts\Javascript
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class DashboardJsFactory extends JavascriptFactory
{
    /**
     * Location of the output template.
     *
     * @var string
     */
    const JS_TEMPLATE = 'dashboard.js';

    /**
     * Creates a new DashboardFactory with the javascript template.
     *
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->template     = self::JS_TEMPLATE;
        $this->templateVars = $dashboard->toArray();

        parent::__construct();
    }
}
