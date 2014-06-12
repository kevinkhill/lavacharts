<h1><?php echo link_to('/', 'Codeigniter gChart Examples'); ?> \ Advanced Line Chart</h1>
<?php
    echo Lava::LineChart('Times')->outputInto('time_div');
    echo Lava::div(800, 500);

    if(Lava::hasErrors())
    {
        echo Lava::getErrors();
    }
?>

<hr />

<h2>Controller Code</h2>
<pre style="font-family:Courier New, monospaced; font-size:10pt;border:1px solid #000;background-color:#f2f2f2;padding:5px;">

</pre>

<h2>View Code</h2>
<pre style="font-family:Courier New, monospaced; font-size:10pt;border:1px solid #000;background-color:#f2f2f2;padding:5px;">
echo Lava::LineChart('Times')->outputInto('time_div');
echo Lava::div(800, 500);

if(Lava::hasErrors())
{
    echo Lava::getErrors();
}
</pre>