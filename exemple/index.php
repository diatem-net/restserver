<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../vendor/autoload.php';
require 'TestController.php';
$server = new \Jacwright\RestServer\RestServer('debug');
$server->addClass('TestController');
$server->handle();