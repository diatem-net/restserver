<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../../../vendor/autoload.php';
require 'cornichons.php';

use \Diatem\RestServer\services\RestAuthService;
use \Diatem\RestServer\RestServer;
use \Diatem\RestServer\RestConfig;

RestConfig::setSecretKey('AEDF6743EABD5E98');
RestConfig::setAppzName('cornichonWorld');
RestConfig::addUser('nomUser', 'EDFA5641EFA76E45', 'standard pro admin');

$server = new RestServer('debug');
$server->addClass('\Diatem\RestServer\services\RestAuthService');
$server->addClass('Cornichons');

$server->handle();
