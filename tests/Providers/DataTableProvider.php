<?php

namespace Khill\Lavacharts\Tests\Providers;

use BadMethodCallException;
use Carbon\Carbon;
use Khill\Lavacharts\DataTables\DataFactory;
use Khill\Lavacharts\DataTables\DataTable;

class DataTableProvider
{
    /**
     * Return a DataTable for the type of chart.
     *
     * @param string $type
     */
    public static function get($type)
    {
        if (method_exists(static::class, $type)) {
            return static::$type();
        } else {
            throw new BadMethodCallException('There is no test data for "'.$type.'"');
        }
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function AnnotationChart()
    {
        $data = new DataTable();
        $data->addColumns([
            ['date',   'Date'],
            ['number', 'Kepler-22b mission'],
            ['string', 'Kepler title'],
            ['string', 'Kepler text'],
            ['number', 'Gliese 163 mission'],
            ['string', 'Gliese title'],
            ['string', 'Gliese text']
        ])->addRows([
            ['2314-2-15', 12400, null, null,
                10645, null, null],
            ['2314-2-16', 24045, 'Lalibertines', 'First encounter',
                12374, null, null],
            ['2314-2-17', 35022, 'Lalibertines', 'They are very tall',
                15766, 'Gallantors', 'First Encounter'],
            ['2314-2-18', 12284, 'Lalibertines', 'Attack on our crew!',
                34334, 'Gallantors', 'Statement of shared principles'],
            ['2314-2-19', 8476, 'Lalibertines', 'Heavy casualties',
                66467, 'Gallantors', 'Mysteries revealed'],
            ['2314-2-20', 0, 'Lalibertines', 'All crew lost',
                79463, 'Gallantors', 'Omniscience achieved']
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function AreaChart()
    {
        $data = new DataTable();
        $data->setDateTimeFormat('Y');
        $data->addDateColumn('Year');
        $data->addNumberColumn('Sales');
        $data->addNumberColumn('Expenses');
        $data->addRows([
            ['2012', 750, 700],
            ['2013', 900, 400],
            ['2014', 1170, 460],
            ['2015', 660,  1120],
            ['2016', 1030, 540]
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function BarChart()
    {
        $data = new DataTable();
        $data->addStringColumn('Food')
             ->addNumberColumn('Votes')
             ->addRow(['Tacos', rand(1000,5000)])
             ->addRow(['Salad', rand(1000,5000)])
             ->addRow(['Pizza', rand(1000,5000)])
             ->addRow(['Apples', rand(1000,5000)])
             ->addRow(['Fish', rand(1000,5000)]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function BubbleChart()
    {
        $data = DataFactory::arrayToDataTable([
            ['ID', 'Life Expectancy', 'Fertility Rate', 'Region',     'Population'],
            ['CAN',    80.66,              1.67,      'North America',  33739900],
            ['DEU',    79.84,              1.36,      'Europe',         81902307],
            ['DNK',    78.6,               1.84,      'Europe',         5523095],
            ['EGY',    72.73,              2.78,      'Middle East',    79716203],
            ['GBR',    80.05,              2,         'Europe',         61801570],
            ['IRN',    72.49,              1.7,       'Middle East',    73137148],
            ['IRQ',    68.09,              4.77,      'Middle East',    31090763],
            ['ISR',    81.55,              2.96,      'Middle East',    7485600],
            ['RUS',    68.6,               1.54,      'Europe',         141850000],
            ['USA',    78.09,              2.05,      'North America',  307007000]
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function CalendarChart()
    {
        $data = new DataTable();
        $data->addDateColumn('Date')
             ->addNumberColumn('Orders');

        foreach (range(2, 5) as $month) {
            for ($a=0; $a < 20; $a++) {
                $day = rand(1, 30);
                $data->addRow(["2014-${month}-${day}", rand(0,100)]);
            }
        }

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function CandlestickChart()
    {
        $data = DataFactory::arrayToDataTable([
            ['Mon', 20, 28, 38, 45],
            ['Tue', 31, 38, 55, 66],
            ['Wed', 50, 55, 77, 80],
            ['Thu', 77, 77, 66, 50],
            ['Fri', 68, 66, 22, 15]
            // Treat first row as data as well.
        ], true);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function ColumnChart()
    {
        $data = new DataTable();
        $data->addColumn('date', 'Year')
             ->addColumn('number', 'Sales')
             ->addColumn('number', 'Expenses')
             ->setDateTimeFormat('Y')
             ->addRow(['2004', 1000, 400])
             ->addRow(['2005', 1170, 460])
             ->addRow(['2006', 660, 1120])
             ->addRow(['2007', 1030, 54]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function ComboChart()
    {
        $data = new DataTable();
        $data->addDateColumn('Year')
             ->addNumberColumn('Sales')
             ->addNumberColumn('Expenses')
             ->addNumberColumn('Net Worth')
             ->addRow(['2009-1-1', 1100, 490, 1324])
             ->addRow(['2010-1-1', 1000, 400, 1524])
             ->addRow(['2011-1-1', 1400, 450, 1351])
             ->addRow(['2012-1-1', 1250, 600, 1243])
             ->addRow(['2013-1-1', 1100, 550, 1462]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function DonutChart()
    {
        return static::PieChart();
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function GanttChart()
    {
        $daysToMilliseconds = function ($days) {
            return $days * 24 * 60 * 60 * 1000;
        };

        $data = DataFactory::DataTable([
            ['string', 'Task ID'],
            ['string', 'Task Name'],
            ['date', 'Start Date'],
            ['date', 'End Date'],
            ['number', 'Duration'],
            ['number', 'Percent Complete'],
            ['string', 'Dependencies'],
        ], [
            ['Research', 'Find sources',
                '2015-1-1', '2015-1-5', null,  100,  null],
            ['Write', 'Write paper',
                null, '2015-1-9', $daysToMilliseconds(3), 25, 'Research,Outline'],
            ['Cite', 'Create bibliography',
                null, '2015-1-7', $daysToMilliseconds(1), 20, 'Research'],
            ['Complete', 'Hand in paper',
                null, '2015-1-10', $daysToMilliseconds(1), 0, 'Cite,Write'],
            ['Outline', 'Outline paper',
                null, '2015-1-6', $daysToMilliseconds(1), 100, 'Research']
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function GaugeChart()
    {
        $data = new DataTable();
        $data->addStringColumn('Type')
             ->addNumberColumn('Value')
             ->addRow(['CPU', rand(0,100)])
             ->addRow(['Case', rand(0,100)])
             ->addRow(['Graphics', rand(0,100)]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function GeoChart()
    {
        $data = new DataTable();
        $data->addStringColumn('Country')
             ->addNumberColumn('Popularity')
             ->addRow(['Germany', 200])
             ->addRow(['United States', 300])
             ->addRow(['Brazil', 400])
             ->addRow(['Canada', 500])
             ->addRow(['France', 600])
             ->addRow(['RU', 700]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function HistogramChart()
    {
        $data = DataFactory::arrayToDataTable([
            ['Dinosaur', 'Length'],
            ['Acrocanthosaurus (top-spined lizard)', 12.2],
            ['Albertosaurus (Alberta lizard)', 9.1],
            ['Allosaurus (other lizard)', 12.2],
            ['Apatosaurus (deceptive lizard)', 22.9],
            ['Archaeopteryx (ancient wing)', 0.9],
            ['Argentinosaurus (Argentina lizard)', 36.6],
            ['Baryonyx (heavy claws)', 9.1],
            ['Brachiosaurus (arm lizard)', 30.5],
            ['Ceratosaurus (horned lizard)', 6.1],
            ['Coelophysis (hollow form)', 2.7],
            ['Compsognathus (elegant jaw)', 0.9],
            ['Deinonychus (terrible claw)', 2.7],
            ['Diplodocus (double beam)', 27.1],
            ['Dromicelomimus (emu mimic)', 3.4],
            ['Gallimimus (fowl mimic)', 5.5],
            ['Mamenchisaurus (Mamenchi lizard)', 21.0],
            ['Megalosaurus (big lizard)', 7.9],
            ['Microvenator (small hunter)', 1.2],
            ['Ornithomimus (bird mimic)', 4.6],
            ['Oviraptor (egg robber)', 1.5],
            ['Plateosaurus (flat lizard)', 7.9],
            ['Sauronithoides (narrow-clawed lizard)', 2.0],
            ['Seismosaurus (tremor lizard)', 45.7],
            ['Spinosaurus (spiny lizard)', 12.2],
            ['Supersaurus (super lizard)', 30.5],
            ['Tyrannosaurus (tyrant lizard)', 15.2],
            ['Ultrasaurus (ultra lizard)', 30.5],
            ['Velociraptor (swift robber)', 1.8]
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function LineChart()
    {
        $data = DataFactory::DataTable([
            ['date', 'Dates'],
            ['number', 'High Temp'],
            ['number', 'Average Temp'],
            ['number', 'Low Temp']
        ], [
            ['2014-10-1',  67, 65, 62],
            ['2014-10-2',  68, 65, 61],
            ['2014-10-3',  68, 62, 55],
            ['2014-10-4',  72, 62, 52],
            ['2014-10-5',  61, 54, 47],
            ['2014-10-6',  70, 58, 45],
            ['2014-10-7',  74, 70, 65],
            ['2014-10-8',  75, 69, 62],
            ['2014-10-9',  69, 63, 56],
            ['2014-10-10', 64, 58, 52],
            ['2014-10-11', 59, 55, 50],
            ['2014-10-12', 65, 56, 46],
            ['2014-10-13', 66, 56, 46],
            ['2014-10-14', 75, 70, 64]
        ], [
            'datetime_format' => 'Y-m-d'
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function OrgChart()
    {
        $data = new DataTable();
        $data->addColumns([
            ['string', 'Name'],
            ['string', 'Manager'],
            ['string', 'ToolTip']
        ]);
        $data->addRows([
            [['Mike', 'Mike<div style="color:red; font-style:italic">President</div>'],
                '', 'The President'],
            [['Jim', 'Jim<div style="color:red; font-style:italic">Vice President</div>'],
                'Mike', 'VP'],
            ['Alice', 'Mike', ''],
            ['Bob', 'Jim', 'Bob Sponge'],
            ['Carol', 'Bob', '']
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function PieChart()
    {
        $data = new DataTable();
        $data->addColumn('string', 'Reasons')
             ->addColumn('number', 'Percent')
             ->addRow(['Check Reviews', 5])
             ->addRow(['Watch Trailers', 2])
             ->addRow(['See Actors Other Work', 4])
             ->addRow(['Settle Argument', 89]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function SankeyChart()
    {
        $data = new DataTable();
        $data->addColumn('string', 'From')
             ->addColumn('string', 'To')
             ->addColumn('number', 'Weight')
             ->addRows([
                 [ 'A', 'X', 5 ],
                 [ 'A', 'Y', 7 ],
                 [ 'A', 'Z', 6 ],
                 [ 'B', 'X', 2 ],
                 [ 'B', 'Y', 9 ],
                 [ 'B', 'Z', 4 ]
             ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function ScatterChart()
    {
        $data = new DataTable();
        $data->addNumberColumn('Age')
             ->addNumberColumn('Weight');

        for ($i=0; $i < 50; $i++) {
            $data->addRow([rand(20, 40), rand(100, 300)]);
        }

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function SteppedAreaChart()
    {
        $data = DataFactory::DataTable([
            ['string', 'Director (Year)'],
            ['number', 'Rotten Tomatoes'],
            ['number', 'IMDB']
        ], [
            ['Alfred Hitchcock (1935)', 8.4, 7.9],
            ['Ralph Thomas (1959)',     6.9, 6.5],
            ['Don Sharp (1978)',        6.5, 6.4],
            ['James Hawes (2008)',      4.4, 6.2]
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function TableChart()
    {
        $data = new DataTable();
        $data->addDateColumn('Month')
             ->addNumberColumn('Donuts Sold')
             ->addRoleColumn('number', 'interval')
             ->addRoleColumn('number', 'interval')
             ->addNumberColumn('Expenses')
             ->addRows([
                 ['2015-1-1', 1000,  900, 1100,  400],
                 ['2015-2-1', 1170, 1000, 1200,  460],
                 ['2015-3-1',  660,  550,  800, 1120],
                 ['2015-4-1', 1030, null, null,  540]
             ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function TimelineChart()
    {
        $data = new DataTable();
        $data->addStringColumn('Room');
        $data->addStringColumn('Name');
        $data->addDateColumn('Start');
        $data->addDateColumn('End');
        $data->addRows([
            [ 'Magnolia Room', 'Beginning JavaScript',       Carbon::parse('12:00pm'), Carbon::parse('1:30pm') ],
            [ 'Magnolia Room', 'Intermediate JavaScript',    Carbon::parse('2:00pm'),  Carbon::parse('3:30pm') ],
            [ 'Magnolia Room', 'Advanced JavaScript',        Carbon::parse('4:00pm'),  Carbon::parse('5:30pm') ],
            [ 'Willow Room',   'Beginning Google Charts',    Carbon::parse('12:30pm'), Carbon::parse('2:30pm') ],
            [ 'Willow Room',   'Intermediate Google Charts', Carbon::parse('3:00pm'), Carbon::parse('4:30pm') ],
            [ 'Willow Room',   'Advanced Google Charts',     Carbon::parse('5:00pm'), Carbon::parse('7:00pm') ]
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function TreeMapChart()
    {
        $data = DataFactory::arrayToDataTable([
            ['Location', 'Parent', 'Market trade volume (size)', 'Market increase/decrease (color)'],
            ['Global',    null,                 0,                               0],
            ['America',   'Global',             0,                               0],
            ['Europe',    'Global',             0,                               0],
            ['Asia',      'Global',             0,                               0],
            ['Australia', 'Global',             0,                               0],
            ['Africa',    'Global',             0,                               0],
            ['Brazil',    'America',            11,                              10],
            ['USA',       'America',            52,                              31],
            ['Mexico',    'America',            24,                              12],
            ['Canada',    'America',            16,                              -23],
            ['France',    'Europe',             42,                              -11],
            ['Germany',   'Europe',             31,                              -2],
            ['Sweden',    'Europe',             22,                              -13],
            ['Italy',     'Europe',             17,                              4],
            ['UK',        'Europe',             21,                              -5],
            ['China',     'Asia',               36,                              4],
            ['Japan',     'Asia',               20,                              -12],
            ['India',     'Asia',               40,                              63],
            ['Laos',      'Asia',               4,                               34],
            ['Mongolia',  'Asia',               1,                               -5],
            ['Israel',    'Asia',               12,                              24],
            ['Iran',      'Asia',               18,                              13],
            ['Pakistan',  'Asia',               11,                              -52],
            ['Egypt',     'Africa',             21,                              0],
            ['S. Africa', 'Africa',             30,                              43],
            ['Sudan',     'Africa',             12,                              2],
            ['Congo',     'Africa',             10,                              12],
            ['Zaire',     'Africa',             8,                               10]
        ]);

        return $data;
    }

    /**
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public static function WordTreeChart()
    {
        $data = DataFactory::arrayToDataTable([
            ['Phrases'],
            ['cats are better than dogs'],
            ['cats eat kibble'],
            ['cats are better than hamsters'],
            ['cats are awesome'],
            ['cats are people too'],
            ['cats eat mice'],
            ['cats meowing'],
            ['cats in the cradle'],
            ['cats eat mice'],
            ['cats in the cradle lyrics'],
            ['cats eat kibble'],
            ['cats for adoption'],
            ['cats are family'],
            ['cats eat mice'],
            ['cats are better than kittens'],
            ['cats are evil'],
            ['cats are weird'],
            ['cats eat mice']
        ]);

        return $data;
    }
}
