<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 16:00
 */

//Config file example

return [
    //Application config
    'app'=>'Flight2wwu_oauth',
    'version'=>'0.1.3',
    'framework_version'=>'0.1.10',
    'author'=>'wwu',
    'description'=>'',
    //Timezone and language
    'timezone'=>'Asia/Shanghai',
    'language'=>'zh_CN',
    //Debug
    'debug'=>1,
    //Maintenance
    'maintain'=>0,
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
        'directory'=>STORAGE . 'log',
        'loggers'=>[
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
        ]
    ],
    //Database
    'database'=>[
        'main'=>[
            'driver'=>'pgsql',
            'host'=>'192.168.0.21',
            'dbname'=>'genoauth_test',
            'user'=>'genobase',
            'password'=>'genobase',
            'port'=>5432
        ],
        'genobase'=>[
            'driver'=>'pgsql',
            'host'=>'192.168.0.21',
            'dbname'=>'genobase',
            'user'=>'genobase',
            'password'=>'genobase',
            'port'=>5432
        ],
        'backup_directory'=>STORAGE . 'backup'
    ],
    //View
    'assets'=>[
        'lib_conf'=>CONFIG . 'ui_libs.php'
    ],
    'view'=>[
        'view_dir'=>APP . 'view',
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
                '/authorize' => 3,
                '/token' => 3,
                '/user/*' => 1,
                '/tool/*' => 1,
                '/apis' => 1,
                '/document' => 1,
                '/genobase/*' => 3,//temp
                '/pharm/*' => 3,//temp
                '/seq/*' =>3,//tmp
            ],
            'admin' => [
                '*' => 3,
                '/admin/*'=>3,
            ],
            'common_user' => [
                '*' => 0,
                '/' => 3,
                '/403' => 3,
                '/admin/*' => 0,
                '/auth/*' => 3,
                '/changelog' => 1,
                '/apis' => 1,
                '/document' => 1,
            ],
        ],
        'session'=>true, //use session to store user info
        'cookie'=>true, //use cookie to store token
    ],
    //OAuth
    'oauth'=>[
        'code_uri'=>'http://192.168.0.21:10000/authorize',
        'token_uri'=>'http://192.168.0.21:10000/token',
        'redirect_uri'=>'http://localhost:8880',
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
        'prefix'=>'oauth',
        'session'=>true, //enable session
        'cookie'=>true, //enable cookie
        'cookie_path'=>'/', // path for cookies
        'cookie_domain'=>null, //domain for cookies
        'old_value'=>'session', //storage method for old_value: cache, session
    ],
    //Mail
    'mail'=>[
        'method'=>'smtp', //method to send mail: mail, sendmail, smtp
        'params'=>[
            'command'=>'/usr/sbin/sendmail -bs', //used for sendmail
            'host'=>'smtp.qiye.163.com', //host for smtp
            'port'=>25, //port for smtp
            'security'=>null, //security for smtp
            'username'=>'no-reply@genowise.com', //username for smtp
            'password'=>'Genowise11', //password for smtp
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