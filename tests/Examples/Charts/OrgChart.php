<?php
    $employees = $lava->DataTable();
    $employees->addColumns([
        ['string', 'Name'],
        ['string', 'Manager'],
        ['string', 'ToolTip']
    ])->addRows([
        [['Mike', 'Mike<div style="color:red; font-style:italic">President</div>'],
           '', 'The President'],
        [['Jim', 'Jim<div style="color:red; font-style:italic">Vice President</div>'],
           'Mike', 'VP'],
        ['Alice', 'Mike', ''],
        ['Bob', 'Jim', 'Bob Sponge'],
        ['Carol', 'Bob', '']
    ]);

    $lava->OrgChart($title, $employees, [
        'title' => 'Company Performance',
        'allowHtml' => true,
        'width' => $width,
        'height' => $height,
        'titleTextStyle' => [
            'color' => '#eb6b2c',
            'fontSize' => 14
        ]
    ]);
