<?php
require './vendor/autoload.php';
use hendrowicaksono\TajukOnline\Topik\Search;
use hendrowicaksono\TajukOnline\Topik\Detail;

Flight::route('/', function(){
  $data['about'] = 'Tajuk online parser';
  Flight::json($data);
});

Flight::route('GET /search/@keyword', function($keyword){
  $search = new Search();
  $data['about'] = 'You search for keyword: '.$keyword;
  $data['result'] = $search->index($keyword);
  Flight::json($data);
});

Flight::route('GET /detail/@tid', function($tid){
  $detail = new Detail();
  $data['about'] = 'Tajuk ID: '.$tid;
  $data['result'] = $detail->index($tid);
  Flight::json($data);
});

Flight::start();
