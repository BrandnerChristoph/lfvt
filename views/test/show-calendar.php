<?php

use yii2fullcalendar\models\Event;
use yii2fullcalendar\yii2fullcalendar;

echo "<h1>Calendar</h1>";

$events = array();
  //Testing
  $Event = new Event();
  $Event->id = 1;
  $Event->title = 'Testing';
  $Event->start = date('Y-m-d\TH:i:s\Z');
  /*
  $Event->nonstandard = [
    'field1' => 'Something I want to be included in object #1',
    'field2' => 'Something I want to be included in object #2',
  ];
  */
  $events[] = $Event;

  $Event = new \yii2fullcalendar\models\Event();
  $Event->id = 2;
  $Event->title = 'Testing';
  $Event->start = date('Y-m-d\TH:i:s\Z',strtotime('tomorrow 6am'));
  $events[] = $Event;

  $events[] = new Event([
                            'id' => 3,
                            'title' => "Test BN",
                            'start' => date('Y-m-d\TH:i:s\Z',strtotime('tomorrow 5am')),
                        ]);

  echo yii2fullcalendar::widget([
    'options' => [
        'lang' => 'de',
        //... more options to be defined here!
      ],
      'events'=> $events,
  ]);