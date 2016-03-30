<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/2
 * Time: 10:57
 */

//ini_set('display_errors', '0');

// define path
define('ROOT', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
define('APP', ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR);
define('CONFIG', APP . 'config' . DIRECTORY_SEPARATOR);
define('WEB', ROOT . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR);
define('VIEW', APP . 'view' . DIRECTORY_SEPARATOR);
define("STORAGE", ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR);
define("LOG", STORAGE . 'log' . DIRECTORY_SEPARATOR);
define("TMP", STORAGE . 'tmp' . DIRECTORY_SEPARATOR);
define('WEBROOT', '/');

// config files
$app_conf = CONFIG . 'app_config.php';

// vendor
require ROOT . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
Flight::set('flight.views.path', VIEW);
require 'helpfunctions.php';

// loader
require '../src/Flight2wwu/Common/Loader.php';
$loader = \Flight2wwu\Common\Loader::getInstance();
$loader->loadConfig($app_conf);
date_default_timezone_set(Flight::get('timezone'));
$loader->registerAll();
