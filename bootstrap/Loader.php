<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 15:31
 */

namespace Flight2wwu;

use Symfony\Component\ClassLoader\Psr4ClassLoader;

/**
 * Class Loader
 * @package Flight2wwu\Common
 */
class Loader
{

    private static $register_path =[
        ['Flight2wwu', 'src' . DIRECTORY_SEPARATOR . 'Flight2wwu', true],
        ['App\Controller', 'App' . DIRECTORY_SEPARATOR . 'Controller', true],
        ['App\Model', 'App' . DIRECTORY_SEPARATOR . 'Model', true],
        ['App\Plugin', 'App' . DIRECTORY_SEPARATOR . 'Plugin', true],
        ['App\Schedule', 'App' . DIRECTORY_SEPARATOR . 'Schedule', true],
    ];

    /**
     * @return Psr4ClassLoader
     */
    public static function getLoader()
    {
        return self::loadClass(self::$register_path);
    }

    /**
     * @param array $dirs [[prefix, path, <recursive>], ...]
     * @return Psr4ClassLoader
     */
    public static function loadClass(array $dirs)
    {
        $loader = new Psr4ClassLoader();
        $root = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..']));
        $paths = [];
        foreach ($dirs as $d) {
            if (is_array($d) && count($d) > 1) {
                $prefix = $d[0];
                $path = $d[1];
                $r = false;
                if (count($d) > 2 && $d[2]) {
                    $r = true;
                }
                $p = self::getClassPath($root, $prefix, $path, $r);
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
    private static function getClassPath($root, $prefix, $dir, $recursive = true)
    {
        $paths = [];
        if ($recursive) {
            $p = self::getPrefixPath($prefix, $root . DIRECTORY_SEPARATOR . $dir);
            $paths = array_merge($paths, $p);
        } else {
            array_push($paths, ['prefix'=>$prefix, 'path'=>$dir]);
        }
        return $paths;
    }
}
