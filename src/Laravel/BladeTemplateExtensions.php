<?php

namespace Khill\Lavacharts\Laravel;

use Illuminate\Support\Facades\App;
use Khill\Lavacharts\Charts\ChartFactory;

/**
 * Blade Template Extensions
 *
 * Charts do not need to be individually called upon to be rendered. They are fully defined before the view
 * so a much cleaner, and shorter syntax can be used in the view for rendering the charts.
 *
 * The @lavacharts() directive will output two <script> tags; The lava.js module and all the necessary javascript
 * for rendering the charts.
 *
 * The @lavajs() is provided as a convenience method, for those who would like to separate the output if the <script>
 * tags, for manual placement.
 *
 *
 * @package    Khill\Lavacharts\Laravel
 * @since      4.0.0 Removed: Laravel 4 support, Chart aliases, renderAll. Added: lavacharts() and lavajs()
 * @since      2.5.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

$app   = App::getFacadeApplication();
$blade = $app['view']->getEngineResolver()->resolve('blade')->getCompiler();

$blade->directive('lavajs', function ($expression) {
    $expression = ltrim($expression, '(');
    $expression = rtrim($expression, ')');

    return "<?php echo \Lava::lavajs($expression); ?>";
});

$blade->directive('lavacharts', function ($expression) {
    $expression = ltrim($expression, '(');
    $expression = rtrim($expression, ')');

    return "<?php echo \Lava::flow($expression); ?>";
});
