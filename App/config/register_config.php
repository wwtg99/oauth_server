<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 7:04
 */

//register files
return [
    /**
     * Register class to Flight, Flight()::name to use
     * name => full class name
     * Class must implement ServiceProvider
     */
    'register_class'=>[
        'Auth' => 'Flight2wwu\Component\Auth\RoleAuth',
        'View' => 'Flight2wwu\Component\View\BorderView',
//        'View' => 'Flight2wwu\Component\View\TwigView',
        'Log' => 'Flight2wwu\Component\Log\Monolog',
//        'DB' => 'Flight2wwu\Component\Database\MedooDB',
        'DB' => 'Flight2wwu\Component\Database\MedooPool',
        'ORM' => 'Flight2wwu\Component\Database\OrmManager',
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
            ["/apis", array('\\App\\Controller\\HomeController', 'apis')],
            ["/document", array('\\App\\Controller\\HomeController', 'document')],
        ],
        /**
         * Register whole controller class with static public functions
         * full class name (without Controller) => prefix
         * All public static functions will register routes by /prefix/function
         * Controller must extends BaseController
         */
        'controller'=>[
            'App\Controller\Auth'=>'auth',
//            'App\Controller\OAuth'=>'oauth',
            'App\Controller\Admin'=>'admin',
            'App\Controller\User'=>'user',
            'App\Controller\Tool'=>'tool',
        ],
    ],
    /**
     * Other handler functions or routes
     */
    'handler'=>CONFIG . 'handlers.php',
];