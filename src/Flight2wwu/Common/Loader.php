<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 15:31
 */

namespace Flight2wwu\Common;

use Flight2wwu\Plugin\PluginManager;
use Symfony\Component\ClassLoader\Psr4ClassLoader;
use Flight2wwu\Component\Utils\FormatUtils;

/**
 * Class Loader
 * @package Flight2wwu\Common
 */
class Loader
{

    /**
     * @var Loader
     */
    private static $instance = null;

    /**
     * @return Loader
     */
    public static function getInstance()
    {
        if (!Loader::$instance) {
            Loader::$instance = new Loader();
        }
        return Loader::$instance;
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
     * Register all from config
     *
     * @throws \Exception
     */
    public function registerAll()
    {
        $config = \Flight::get('config');
        // register path
        $c = $config['register_path'];
        $this->loadClass($c);
        // register class
        $c = $config['register_class'];
        $this->registerClass($c);
        // register route
        $route = $config['route'];
        $this->registerRoute($route['path']);
        $this->registerRouteController($route['controller']);
        $route_file = $route['file'];
        if (file_exists($route_file)) {
            require "$route_file";
        }
        // handler
        $handler_file = $config['handler'];
        if (file_exists($handler_file)) {
            require "$handler_file";
        }
        // plugin
        $pc = \Flight::get('plugin');
        $this->registerPlugin($pc['config']);
    }

    /**
     * @param array $arr
     */
    public function registerClass($arr)
    {
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
    public function registerRoute($arr)
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
    public function registerRouteController($arr)
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
     * @param array $dirs [[prefix, path, <recursive>], ...]
     * @return Psr4ClassLoader
     */
    public function loadClass(array $dirs)
    {
        $loader = new Psr4ClassLoader();
        $paths = [];
        foreach ($dirs as $d) {
            if (is_array($d) && count($d) > 1) {
                $prefix = $d[0];
                $path = $d[1];
                $r = false;
                if (count($d) > 2 && $d[2]) {
                    $r = true;
                }
                $p = $this->getClassPath(ROOT, $prefix, $path, $r);
                $paths = array_merge($paths, $p);
            }
        }
        foreach ($paths as $p) {
            $loader->addPrefix($p['prefix'], $p['path']);
        }
        $loader->register();
        return $loader;
    }

    /**
     * @param string $file
     */
    public function registerPlugin($file)
    {
        if (file_exists($file)) {
            PluginManager::loadConfig($file);
        }
    }

    /**
     * @param $prefix
     * @param $realPath
     * @return array
     */
    private static function getPrefixPath($prefix, $realPath)
    {
        $pp = [];
        array_push($pp, ['prefix'=>$prefix, 'path'=>$realPath]);
        $di = new \DirectoryIterator($realPath);
        foreach($di as $f) {
            if ($f->isDot()) continue;
            if ($f->isFile()) continue;
            if (strpos(basename($f), '.') === 0) continue;
            if ($f->isDir()) {
                //load if first letter is upper case
                $fc = substr($f->getBasename(), 0, 1);
                if ($fc > 64 && $fc < 91) {
                    $pp = array_merge($pp, self::getPrefixPath($prefix . '\\' . $f, $realPath . DIRECTORY_SEPARATOR . $f));
                }
            }
        }
        return $pp;
    }

    /**
     * @param string $root
     * @param string $prefix
     * @param string $dir
     * @param bool $recursive
     * @return array
     */
    private function getClassPath($root, $prefix, $dir, $recursive = true)
    {
        $paths = [];
        if ($recursive) {
            $p = $this->getPrefixPath($prefix, $root . DIRECTORY_SEPARATOR . $dir);
            $paths = array_merge($paths, $p);
        } else {
            array_push($paths, ['prefix'=>$prefix, 'path'=>$dir]);
        }
        return $paths;
    }
}
