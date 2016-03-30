<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 16:00
 */

return [
    //Application config
    'app'=>'Flight2wwu_OAuth',
    'version'=>'0.1.0',
    'author'=>'wwu',
    'description'=>'',
    //Timezone and language
    'timezone'=>'Asia/Shanghai',
    'language'=>'zh_CN',
    //Debug
    'debug'=>1,
    //Config files
    'config'=>[
        /**
         * Register classes
         * [prefix, path, <recursive>]
         * prefix for namespace
         * path is relative to project root
         * recursive default is false, true to register all subdirectories with first letter uppercase
         */
        'register_path'=>[
            ['Flight2wwu', 'src' . DIRECTORY_SEPARATOR . 'Flight2wwu', true],
            ['App\Controller', 'App' . DIRECTORY_SEPARATOR . 'Controller', true],
            ['App\Model', 'App' . DIRECTORY_SEPARATOR . 'Model', true],
            ['App\Plugin', 'App' . DIRECTORY_SEPARATOR . 'Plugin', true],
            ['App\Schedule', 'App' . DIRECTORY_SEPARATOR . 'Schedule', true],
        ],
        /**
         * Register class to Flight, Flight();:name to use
         * name => full class name
         * Class must implement ServiceProvider
         */
        'register_class'=>[
            'Auth' => 'Flight2wwu\Component\Auth\RoleAuth',
            'View' => 'Flight2wwu\Component\View\BorderView',
            'Log' => 'Flight2wwu\Component\Log\Monolog',
//            'DB' => 'Flight2wwu\Component\Database\PdoDB',
            'DB' => 'Flight2wwu\Component\Database\MedooDB',
            'Locale' => 'Flight2wwu\Component\Translation\SymTrans',
            'Cache' => 'Flight2wwu\Component\Storage\Cache',
            'Session' => 'Flight2wwu\Component\Storage\SessionUtil',
            'Cookie' => 'Flight2wwu\Component\Storage\CookieUtil',
            'Value' => 'Flight2wwu\Component\Storage\OldValue',
            'Assets' => 'Flight2wwu\Component\View\AssetsManager',
            'Mail' => 'Flight2wwu\Component\Utils\Mail',
            'Express' => 'Flight2wwu\Component\Utils\Express',
        ],
        /**
         * Register route here
         * All path will be registered in sequence
         */
        'route'=>[
            /**
             * Route path for functions
             * [route, array(full class name, function name)]
             */
            'path'=>[
                ["*", array('\\App\\Controller\\HomeController', 'rbac')],
                ["*", array('\\App\\Controller\\HomeController', 'language')],
                ["/", array('\\App\\Controller\\HomeController', 'home')],
                ["/home", array('\\App\\Controller\\HomeController', 'home')],
                ["/403", array('\\App\\Controller\\HomeController', 'forbidden')],
                ["/changelog", array('\\App\\Controller\\HomeController', 'changelog')],
                ["/authorize", array('\\App\\Controller\\AuthorizeController', 'authorize')],
                ["/token", array('\\App\\Controller\\AuthorizeController', 'token')],
            ],
            /**
             * Register whole controller class with static public functions
             * full class name (without Controller) => prefix
             * All public static functions will register routes by /prefix/function
             * Controller must extends BaseController
             */
            'controller'=>[
                'App\Controller\Auth'=>'auth',
//                'App\Controller\OAuth'=>'oauth',
                'App\Controller\Admin'=>'admin',
                'App\Controller\User'=>'user',
            ],
            /**
             * Other route definition file
             */
            'file'=>CONFIG . 'route.php',
        ],
        /**
         * Other handler functions
         */
        'handler'=>CONFIG . 'handlers.php',
    ],
    //Plugin
    'plugin'=>[
        'config'=>CONFIG . 'plugins.json'
    ],
    //Schedule
    'schedule'=>[
        'support'=>'crontab',  // support program, only support for crontab now
        'time'=>'0 0 */1 * *',  // interval time
        'config'=>CONFIG . 'schedule.json',  // schedule config file
        'engine'=>'schedule_engine.php'  // run script in bin
    ],
    //Log
    'log'=>[
        'main'=>[
            'title'=>'main.log',
            'max_logfile'=>10,
            'level'=>'debug'
        ],
        'access'=>[
            'title'=>'access.log',
            'max_logfile'=>30,
            'level'=>'info'
        ],
        'database'=>[
            'title'=>'database.log',
            'max_logfile'=>5,
            'level'=>'debug'
        ]
    ],
    //Database
    'database'=>[
        'main'=>[
            'driver'=>'pgsql',
            'host'=>'192.168.0.21',
            'dbname'=>'genoauth',
            'user'=>'lims_gw',
            'password'=>'1',
            'port'=>5432
        ]
    ],
    //View
    'assets'=>[
        'lib_conf'=>CONFIG . 'ui_libs.php'
    ],
    //Auth
    'auth'=>[
        /**
         * Define role based access control
         * role_name => ['object or path' => auth]
         *
         * auth
         * 0: forbidden
         * 1: GET access or enable
         * 2: POST access
         * 3: 1 | 2
         *
         * For path
         * 1. check exact same path
         * 2. if not found, check upper dir /*
         * 3. if all upper dir is not found, check *
         */
        'rbac'=>[
            'anonymous'=>[
                '/' => 3,
                '/403' => 3,
                '/auth/login' => 3,
                '/auth/logout' => 3,
                '/authorize' => 3,
                '/token' => 3,
            ],
            'admin' => [
                '*' => 3
            ],
            'common_user' => [
                '*' => 0,
                '/' => 3,
                '/403' => 3,
                '/admin/*' => 0,
                '/auth/*' => 3,
                '/oauth/*' => 3,
                '/changelog' => 1,
            ],
        ],
        'session'=>true, //use session to store user info
        'cookie'=>false, //use cookie to store token
    ],
    //OAuth
    'oauth'=>[
        'code_uri'=>'',
        'token_uri'=>'',
        'redirect_uri'=>'',
        'redirect_uri_key'=>'redirect_uri',
        'app_id'=>'',
        'app_id_key'=>'client_id',
        'app_secret'=>'',
        'app_secret_key'=>'client_secret',
        'state_key'=>'state',
    ],
    //Storage
    'storage'=>[
        'cache'=>[
            'adapter'=>'File', //Adapter for Desarrolla2\Cache: File, Apcu
            'params'=>[
                'cacheDir'=>TMP . 'cache',
                'ttl'=>3600
            ]
        ],
        'session'=>true, //enable session
        'cookie'=>true, //enable cookie
        'old_value'=>'session', //storage method for old_value: cache, session
    ],
    //Mail
    'mail'=>[
        'method'=>'mail', //method to send mail: mail, sendmail, smtp
        'params'=>[
            'command'=>'/usr/sbin/sendmail -bs', //used for sendmail
            'host'=>'localhost', //host for smtp
            'port'=>25, //port for smtp
            'security'=>null, //security for smtp
            'username'=>'', //username for smtp
            'password'=>'', //password for smtp
        ],
    ],
    //Express
    //[name, label, request url (<no> will be replaced), format function name]
    'express'=>[
        ['yto', '圆通', 'http://www.kiees.cn/yto.php?wen=<no>&action=ajax', 'extractKieesTable'],
        ['sto', '申通', 'http://www.kiees.cn/sto.php?wen=<no>&ajax=1', 'extractKieesTable'],
        ['ems', 'EMS', 'http://www.kiees.cn/ems.php?wen=<no>&action=ajax', 'extractKieesTable'],
        ['sf', '顺风', 'http://www.kiees.cn/sf.php?wen=<no>&action=ajax', 'extractKieesTable'],
        ['zto', '中通', 'http://www.kiees.cn/zto.php?wen=<no>&action=ajax', 'extractKieesTable'],
        ['yd', '韵达', 'http://www.kiees.cn/yd.php?wen=<no>&channel', 'extractKieesTable'],
    ],
];