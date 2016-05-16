<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 6:50
 */

namespace Flight2wwu\Common;


use Flight2wwu\Component\Utils\FormatUtils;
use Flight2wwu\Plugin\PluginManager;

class Register
{

    /**
     * @var Register
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @return Register
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Register();
        }
        return self::$instance;
    }

    /**
     * @param string|array $conf
     */
    public function loadConfig($conf)
    {
        if (is_array($conf)) {
            foreach ($conf as $k => $v) {
                \Flight::set($k, $v);
            }
        } elseif (file_exists($conf)) {
            $c = require "$conf";
            foreach ($c as $k => $v) {
                \Flight::set($k, $v);
            }
        }
    }

    /**
     * Register all in config/register_config.php.
     */
    public function registerAll()
    {
        date_default_timezone_set(\Flight::get('timezone'));
        $f = CONFIG . 'register_config.php';
        $this->load($f);
        // plugin
        $pc = \Flight::get('plugin');
        $f = isset($pc['config']) ? FormatUtils::formatPath($pc['config']) : '';
        $this->registerPlugin($f);
    }

    /**
     * load and register from config
     *
     * @param string|array $conf
     * @throws \Exception
     */
    public function load($conf)
    {
        if (is_array($conf)) {
            $this->config = $conf;
        } elseif (file_exists($conf)) {
            $this->config = require "$conf";
        }
        // register class
        $c = isset($this->config['register_class']) ? $this->config['register_class'] : [];
        $this->registerClass($c);
        // register route
        $route = $this->config['route'] ? $this->config['route'] : [];
        $route_path = isset($route['path']) ? $route['path'] : [];
        $route_controller = isset($route['controller']) ? $route['controller'] : [];
        $this->registerRoute($route_path);
        $this->registerRouteController($route_controller);
        // handler
        $handler_file = isset($this->config['handler']) ? FormatUtils::formatPath($this->config['handler']) : '';
        if ($handler_file && file_exists($handler_file)) {
            require "$handler_file";
        }
    }

    /**
     * @param array $arr
     */
    public function registerClass(array $arr)
    {
        if (!$arr) {
            return;
        }
        $boot = [];
        //register
        foreach ($arr as $k => $v) {
            \Flight::register($k, $v);
            $ins = \Flight::$k();
            if ($ins instanceof ServiceProvider) {
                $ins->register();
                array_push($boot, $k);
            }
        }
        //boot
        foreach ($boot as $k) {
            $ins = \Flight::$k();
            if ($ins instanceof ServiceProvider) {
                $ins->boot();;
            }
        }
    }

    /**
     * @param array $arr
     */
    public function registerRoute(array $arr)
    {
        foreach ($arr as $i => $v) {
            if (is_array($v) && count($v) > 1) {
                \Flight::route($v[0], $v[1]);
            }
        }
    }

    /**
     * @param array $arr
     */
    public function registerRouteController(array $arr)
    {
        foreach ($arr as $k => $v) {
            $classname = $k . 'Controller';
            $ref = new \ReflectionClass($classname);
            if ($ref->isSubclassOf('Flight2wwu\Common\BaseController')) {
                $prefix = FormatUtils::formatWebPath($v);
                $methods = $ref->getMethods(\ReflectionMethod::IS_STATIC);
                foreach ($methods as $m) {
                    if ($m->isPublic()) {
                        $path = $prefix . $m->getName();
                        \Flight::route($path, [$classname, $m->getName()]);
                    }
                }
            }
        }
    }

    /**
     * @param string $file
     */
    public function registerPlugin($file)
    {
        if ($file && file_exists($file)) {
            PluginManager::loadConfig($file);
        }
    }
}