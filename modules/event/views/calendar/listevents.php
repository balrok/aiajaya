<?php
/*
        'kind' => 'calendar#event',
        'etag' => '\"ZrhdJMCgpoUK_a5fT7XOC6xn46g/Z2NhbDAwMDAxMzYwMzUxMjQyMzE2MDAw\"',
        'id' => 'nri1gro7lgovna5av256sr03d0',
        'status' => 'confirmed',
        'htmlLink' => 'https://www.google.com/calendar/event?eid=bnJpMWdybzdsZ292bmE1YXYyNTZzcjAzZDAgZHVkdGI1NzlyNjcyZXY2Y2V1NGwzbnA2bG9AZw',
        'created' => '2013-02-08T19:20:42.000Z',
        'updated' => '2013-02-08T19:20:42.316Z',
        'summary' => 'test',
        'creator' => array(
            'email' => 'balrok.1787569@gmail.com',
        ),
        'organizer' => array(
            'email' => 'dudtb579r672ev6ceu4l3np6lo@group.calendar.google.com',
            'displayName' => 'Balance Termine',
            'self' => true,
        ),
        'start' => array(
            'dateTime' => '2013-02-08T20:20:42+01:00',
        ),
        'end' => array(
            'dateTime' => '2013-02-08T21:20:42+01:00',
        ),
        'iCalUID' => 'nri1gro7lgovna5av256sr03d0@google.com',
        'sequence' => 0,
        'reminders' => array(
            'useDefault' => true,
        ),
*/
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$items,
    'columns'=>array(
        'id',
        array(
			'name'=>'Status',
			'value'=>'str_replace("confirmed", "OK", $data["status"])',
		),
        'summary',
        array(
			'name'=>'Von',
			'value'=>'date("d.m.y G:i", strtotime($data["start"]["dateTime"]))',
		),
        array(
			'name'=>'Bis',
			'value'=>'date("d.m.y G:i", strtotime($data["end"]["dateTime"]))',
		),
        //array(            // display 'create_time' using an expression
        //    'name'=>'create_time',
        //    'value'=>'date("M j, Y", $data->create_time)',
        //),
    ),
));


?>
