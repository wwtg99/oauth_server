<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/2
 * Time: 10:55
 */

require '../bootstrap/init.php';

if (Flight::get('maintain')) {
    Flight::halt(503, 'Maintenance');
} else {
    Flight::start();
}
