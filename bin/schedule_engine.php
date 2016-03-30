<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 16:24
 */

$opt = [
    '-h' => '--help',
    '-v' => '--version',
    'register' => '',
    'unregister' => ''
];

if ($argc > 1) {
    $cmd = strtolower($argv[1]);
    if (!array_key_exists($cmd, $opt)) {
        $cmd = array_search($cmd, $opt);
        if (!$cmd) {
            print_help();
        }
    }
    switch ($cmd) {
        case '-h': print_help(); break;
        case '-v': print_version(); break;
        case 'register': register_service(); break;
        case 'unregister': unregister_service(); break;
        default: print_help(); break;
    }
} else {
    run_schedule();
}

function print_help() {
    echo "Run schedule command\n --help -h\tprint help\n --version -v\tprint version\n register\tregister schedule service\n unregister\tunregister schedule service\n";
}

function print_version() {
    echo "0.1.0\n";
}

function register_service() {
    chdir(realpath(__DIR__));
    require "../bootstrap/init.php";
    $app_conf = CONFIG . 'app_config.php';
    $manager = new \Flight2wwu\Schedule\ScheduleManager($app_conf);
    $manager->enableService();
    echo "register service\n";
}

function unregister_service() {
    chdir(realpath(__DIR__));
    require "../bootstrap/init.php";
    $app_conf = CONFIG . 'app_config.php';
    $manager = new \Flight2wwu\Schedule\ScheduleManager($app_conf);
    $manager->disableService();
    echo "unregister service\n";
}

function run_schedule() {
    chdir(realpath(__DIR__));
    require "../bootstrap/init.php";
    $app_conf = CONFIG . 'app_config.php';
    $manager = new \Flight2wwu\Schedule\ScheduleManager($app_conf);
    $manager->register();
    $manager->run();
}
