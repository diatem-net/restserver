<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../vendor/autoload.php';
session_start();

use Jin2\Com\Curl;

$url = 'http://172.31.6.56/_sandbox/resttest/exemple/rest/v1/login';
$args = array(
    'userID'  =>  'nomUser',
    'userKey' =>   'EDFA5641EFA76E45'
);
$requestType = Curl::CURL_REQUEST_TYPE_POST;
$throwErrors = true;
$httpAuthUser = null;
$httpAuthPassword = null;
$contentType = null;
$headers = array();
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
$res = json_decode($res, true);
echo '<hr>';
$_SESSION['jwt'] = $res['jwt'];

