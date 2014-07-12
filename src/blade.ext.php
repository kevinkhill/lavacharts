<?php

Blade::extend(function($view, $compiler)
{
    $pattern = $compiler->createMatcher('render');

    return preg_replace($pattern, '<?php echo Lava::render$2; ?>', $view);
});
