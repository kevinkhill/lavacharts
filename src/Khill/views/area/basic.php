<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LavaCharts - Area - Basic</title>
    <?php
        echo HTML::style('css/site.css');
        echo HTML::style('css/prettify.dark.css');
        echo HTML::style('css/examples.css');
        echo HTML::script('js/jquery.js');
        echo HTML::script('js/prettify.run.js');
        echo HTML::script('js/jquery.zclip.js');
    ?>
</head>

<body>
    <a href="https://github.com/kevinkhill/" id="forkMe">
        <img src="<?php echo asset('images/forkme.png'); ?>" alt="Fork me on GitHub">
    </a>

    <h1 class="exampleTitle">
        <?php echo link_to('/lavacharts/examples', 'LavaChart Examples'); ?> \ Basic A Chart
    </h1>
    <?php
        echo Lava::AreaChart('Stocks')->outputInto('stock_div');
        echo Lava::div(1000, 400);

        if(Lava::hasErrors())
        {
            echo Lava::getErrors();
        }
    ?>

<br />

<div class="prettyprintContainer">
<span class="prettyprintContainerLabel">Closure Route/Controller</span>
<div class="prettyprintCode">
<pre class="prettyprint linenums">
$stocksTable = Lava::DataTable('Stocks');

$stocksTable->addColumn('date', 'Date', 'date')
            ->addColumn('number', 'Projected', 'projected')
            ->addColumn('number', 'Closing', 'closing');

for($a = 1; $a < 30; $a++)
{
    $data = array(
        new jsDate(2011, 5, $a), //Date
        rand(9500,10000),        //Area 1's data
        rand(9500,10000)         //Area 2's data
    );

    $stocksTable->addRow($data);
}

Lava::AreaChart('Stocks')->title('Stock Market Trends');
</pre>
</div>
</div>


<div class="prettyprintContainer">
<span class="prettyprintContainerLabel">View</span>
<div class="prettyprintCode">
<!--<div id="clip_button" data-clipboard-target="clip_controller">Copy To Clipboard</div>-->
<pre id="clip_controller" class="prettyprint linenums">
echo Lava::AreaChart('Stocks')->outputInto('stock_div');
echo Lava::div(1000, 400);

if(Lava::hasErrors())
{
    echo Lava::getErrors();
}
</pre>
</div>
</div>


<script type="text/javascript">////
   /* $(function() {
        ZeroClipboard.setDefaults({
            moviePath: "<?php URL::to('/'); ?>/js/ZeroClipboard.swf",
            forceHandCursor: true
        });

        var clip = new ZeroClipboard($('#clip_button'));

        clip.on( 'load', function(client) {
//         alert( "movie is loaded" );
        } );

        clip.on( 'complete', function(client, args) {
            this.style.display = 'none'; // "this" is the element that was clicked
            alert("Copied text to clipboard: " + args.text );
        } );

        clip.on( 'mouseover', function(client) {
//         alert("mouse over");
        } );

        clip.on( 'mouseout', function(client) {
//         alert("mouse out");
        } );

        clip.on( 'mousedown', function(client) {

//         alert("mouse down");
        } );

        clip.on( 'mouseup', function(client) {
//         alert("mouse up");
        } );
    });*/
</script>

</body>
</html>