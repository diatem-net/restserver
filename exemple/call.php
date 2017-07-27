<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../vendor/autoload.php';
session_start();

use Jin2\Com\Curl;




$url = 'http://172.31.6.56/_sandbox/resttest/exemple/rest/v1/cornichons';

$args = array(
    'nom'  =>  'A',
    'debug' =>  true
);
$requestType = Curl::CURL_REQUEST_TYPE_POST;
$throwErrors = true;
$httpAuthUser = null;
$httpAuthPassword = null;
$contentType = null;
$headers = array(
    'Authorization'   =>  $_SESSION['jwt']
);

$outputTraceFile = 'log.txt';
$followLocation = false;

$res = Curl::call( $url, 
            $args, 
            $requestType,
            $throwErrors, 
            $httpAuthUser, 
            $httpAuthPassword, 
            $contentType, 
            $headers, 
            $outputTraceFile, 
            $followLocation );

var_dump('HTTP CODE : '.Curl::getLastHttpCode());
echo '<hr>';
var_dump($res);
