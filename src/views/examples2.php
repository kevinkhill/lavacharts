<!DOCTYPE html>
<html>
<head>
<title>LavaCharts - Examples</title>
<meta name="keywords" content="chart, laravel, bundle, package, composer, php" />
<meta name="description" content="Charts & Graphs for Laravel powered by the Google Chart API." />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1 user-scalable=no">
<?php
    echo HTML::style('css/site.css');
    echo HTML::style('css/home.css');
    echo HTML::style('css/smint.css');
    echo HTML::style('css/prettify.dark.css');
    echo HTML::style('css/examples.css');
    echo HTML::script('js/jquery.js');
    echo HTML::script('js/jquery.smint.js');
    echo HTML::script('js/prettify.run.js');
?>

<script type="text/javascript">
    $(document).ready( function() {
        $('.subMenu').smint({
            'scrollSpeed' : 1000
        });

        $(window).on('scroll', function() {
            var st = $(this).scrollTop()-180;
            $('.welcome').css({
                'opacity' : (1 - st/120)
            });
        });

        $('.prettyprintContainerLabel').each(function(){
            var containerLabel = $(this);
            var containerWrapper = containerLabel.parent('div.prettyprintContainer');
            var codeContainer = containerWrapper.find('div.prettyprintCode');
            var section = containerWrapper.parent('div.section');

            containerLabel.click(function(){
                if(codeContainer.is(":visible")) { //Visible, Hidding
                    containerLabel.removeClass('collapse').addClass('expand');
                    codeContainer.slideUp(300, function() {
                        containerWrapper.addClass('hidden');
                    });
                } else { //Hidden, Opening
                    containerLabel.removeClass('expand').addClass('collapse');
                    containerWrapper.removeClass('hidden');
                    codeContainer.slideDown(600);
                }
            });
        });

    });
</script>

</head>
<body onload="setTimeout(function() { window.scrollTo(0, 0) }, 100);">
    <a href="https://github.com/kevinkhill/LavaCharts" id="forkMe">
        <img src="<?php echo url($lavaAssetPath.'images/forkme.png'); ?>" alt="Fork me on GitHub">
    </a>
    <div class="wrap">
        <div class="subMenu" >
            <div class="inner">
                <a href="#" id="sTop" class="subNavBtn">LavaCharts</a>
                <a href="#" id="s1" class="subNavBtn">Line</a>
                <a href="#" id="s2" class="subNavBtn">Area</a>
                <a href="#" id="s3" class="subNavBtn">Pie</a>
                <a href="#" id="s4" class="subNavBtn">Column</a>
                <a href="#" id="s5" class="subNavBtn end">Geo</a>
            </div>
        </div>

<!-- Top Section -->
	<div class="section sTop">
            <div class="inner">
                <div class="welcome">
                    <table>
                        <tr>
                            <td>
                                <a href="http://laravel.com" title="Laravel PHP Framework">
                                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIcAAACHCAYAAAA850oKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyNUVCMTdGOUJBNkExMUUyOTY3MkMyQjZGOTYyREVGMiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyNUVCMTdGQUJBNkExMUUyOTY3MkMyQjZGOTYyREVGMiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjI1RUIxN0Y3QkE2QTExRTI5NjcyQzJCNkY5NjJERUYyIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjI1RUIxN0Y4QkE2QTExRTI5NjcyQzJCNkY5NjJERUYyIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+g6J7EAAAEL1JREFUeNrsXQmU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">
                                </a>
                            </td>
                            <td style="vertical-align: middle;">
                                <span id="plus">+</span>
                            </td>
                            <td>
                                <a href="https://developers.google.com/chart/interactive/docs/gallery" title="Google Chart Gallery">
                                    <img src="data:image/png;base64,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">
                                </a>
                            </td>
                        </tr>
                    </table>
                    <h1>LavaChart Examples</h1>
                    <h2>Demos & Examples of<br />LavaCharts in Laravel</h2>
                </div>
            </div>
            <br class="clear">
	</div>
<!-- /Top Section -->


<!-- Line Charts -->
	<div class="section s1">
            <div class="inner">
                <h1 class="exampleHeading">Line Charts</h1>
    <!-- Basic -->
                <h2>Basic Example</h2>
                <a href="<?php echo asset($lavaAssetPath.'images/examples/line.basic.png'); ?>" title="Basic Line Chart Example">
                    <img src="<?php echo asset($lavaAssetPath.'images/examples/line.basic.png'); ?>" />
                </a>
                <br />

                <div class="prettyprintContainer hidden">
                    <span class="prettyprintContainerLabel expand">
                        Route Closure / Controller
                    </span>
                    <div class="prettyprintCode" style="display:none;">
                        <pre class="prettyprint linenums">
$stocksTable = Lava::DataTable('Stocks');

$stocksTable->addColumn('date', 'Date', 'date')
            ->addColumn('number', 'Projected', 'projected')
            ->addColumn('number', 'Closing', 'closing');

for($a = 1; $a < 30; $a++)
{
    $data = array(
        new jsDate(2011, 5, $a), //Date
        rand(9500,10000),        //Line 1's data
        rand(9500,10000)         //Line 2's data
    );

    $stocksTable->addRow($data);
}

Lava::LineChart('Stocks')->title('Stock Market Trends');</pre>
                    </div>
                </div>


                <div class="prettyprintContainer hidden">
                    <span class="prettyprintContainerLabel expand">
                        View
                    </span>
                    <div class="prettyprintCode" style="display:none;">
                <!--<div id="clip_button" data-clipboard-target="clip_controller">Copy To Clipboard</div>-->
                        <pre id="clip_controller" class="prettyprint linenums">
echo Lava::LineChart('Stocks')->outputInto('stock_div');
echo Lava::div(1000, 400);

if(Lava::hasErrors())
{
    echo Lava::getErrors();
}</pre>
                    </div>
                </div>
    <!-- /Basic -->

    <!-- Advanced -->
                <h2>Advanced Example</h2>
                <a href="<?php echo asset($lavaAssetPath.'images/examples/line.advanced.png'); ?>">
                    <img src="<?php echo asset($lavaAssetPath.'images/examples/line.advanced.png'); ?>" />
                </a>
                <br />

                <div class="prettyprintContainer hidden">
                    <span class="prettyprintContainerLabel expand">
                        Route Closure / Controller
                    </span>
                    <div class="prettyprintCode" style="display:none;">
                        <pre class="prettyprint linenums">
$timesTable = Lava::DataTable('Times');

$timesTable->addColumn('date', 'Dates', 'dates')
           ->addColumn('number', 'Estimated Time', 'schedule')
           ->addColumn('number', 'Actual Time', 'run');

for($a = 1; $a < 30; $a++)
{
    $data = array(
        Lava::jsDate(2013, 8, $a), //Date object
        rand(5,30),                //Line 1's data
        rand(5,30),                //Line 2's data
    );

    $timesTable->addRow($data);
}

//Either Chain functions together and assign to variables
$legendStyle = Lava::textStyle()->color('#F3BB00')
                                ->fontName('Arial')
                                ->fontSize(20);

$legend = Lava::legend()->position('bottom')
                        ->alignment('start')
                        ->textStyle($legendStyle);


//Or pass in arrays with set options into the function's constructor
$tooltip = Lava::tooltip(array(
                'showColorCode' => true,
                'textStyle' => Lava::textStyle(array(
                    'color' => '#C0C0B0',
                    'fontName' => 'Courier New',
                    'fontSize' => 10
                ))
            ));


$config = array(
    'backgroundColor' => Lava::backgroundColor(array(
        'stroke' => '#113bc9',
        'strokeWidth' => 4,
        'fill' => '#ffd'
    )),
    'chartArea' => Lava::chartArea(array(
        'left' => 100,
        'top' => 75,
        'width' => '85%',
        'height' => '55%'
    )),
    'titleTextStyle' => Lava::textStyle(array(
        'color' => '#FF0A04',
        'fontName' => 'Georgia',
        'fontSize' => 18
    )),
    'legend' => $legend,
    'tooltip' => $tooltip,
    'title' => 'Times for Deliveries',
    'titlePosition' => 'out',
    'curveType' => 'function',
    'width' => 1000,
    'height' => 450,
    'pointSize' => 3,
    'lineWidth' => 1,
    'colors' => array('#4F9CBB', 'green'),
    'hAxis' => Lava::hAxis(array(
        'baselineColor' => '#fc32b0',
        'gridlines' => array(
            'color' => '#43fc72',
            'count' => 6
        ),
        'minorGridlines' => array(
            'color' => '#b3c8d1',
            'count' => 3
        ),
        'textPosition' => 'out',
        'textStyle' => Lava::textStyle(array(
            'color' => '#C42B5F',
            'fontName' => 'Tahoma',
            'fontSize' => 10
        )),
        'slantedText' => true,
        'slantedTextAngle' => 30,
        'title' => 'Delivery Dates',
        'titleTextStyle' => Lava::textStyle(array(
            'color' => '#BB33CC',
            'fontName' => 'Impact',
            'fontSize' => 14
        )),
        'maxAlternation' => 6,
        'maxTextLines' => 2
    )),
    'vAxis' => Lava::vAxis(array(
        'baseline' => 5,
        'baselineColor' => '#CF3BBB',
        'format' => '## Min.',
        'textPosition' => 'out',
        'textStyle' => Lava::textStyle(array(
            'color' => '#DDAA88',
            'fontName' => 'Arial Bold',
            'fontSize' => 10
        )),
        'title' => 'Delivery Time',
        'titleTextStyle' => Lava::textStyle(array(
            'color' => '#5C6DAB',
            'fontName' => 'Verdana',
            'fontSize' => 14
        )),
    ))
);

Lava::LineChart('Times')->setConfig($config);
                        </pre>
                    </div>
                </div>


                <div class="prettyprintContainer hidden">
                    <span class="prettyprintContainerLabel expand">
                        View
                    </span>
                    <div class="prettyprintCode" style="display:none;">
                        <pre id="clip_controller" class="prettyprint linenums">
echo Lava::LineChart('Times')->outputInto('time_div');
echo Lava::div(800, 500);

if(Lava::hasErrors())
{
    echo Lava::getErrors();
}</pre>
                    </div>
                </div>
    <!-- /Advanced -->
            </div>
	</div>
<!-- /Line Charts -->

	<div class="section s2">
            <div class="inner">
                <h1 class="exampleHeading">Area Charts</h1>
                <h2>Basic</h2>
                <h2>Advanced</h2>
            </div>
	</div>
<!--End Area Charts-->

	<div class="section s3">
            <div class="inner">
                <h1 class="exampleHeading">Pie Charts</h1>
                <h2>Basic</h2>
                <h2>Advanced</h2>
            </div>
	</div>
<!--End Pie Charts-->

	<div class="section s4">
            <div class="inner">
                <h1 class="exampleHeading">Column Charts</h1>
                <h2>Basic</h2>
                <h2>Advanced</h2>
            </div>
	</div>
<!--End Column Charts-->

	<div class="section s5">
            <div class="inner">
                <h1 class="exampleHeading">Geo Charts</h1>
                <h2>Basic</h2>
                <h2>Advanced</h2>
            </div>
	</div>
<!--End Geo Charts-->

</div>
</body>
</html>
