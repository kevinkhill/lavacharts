<?php

namespace Khill\Lavacharts\Charts;

/**
 * AnnotationChart Class
 *
 * Annotation charts are interactive time series line charts that support annotations.
 * Unlike the annotated timeline, which uses Flash, annotation charts are SVG/VML and
 * should be preferred whenever possible.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     3.0.5
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class AnnotationChart extends Chart
{
    /**
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return 'annotationchart';
    }
}
