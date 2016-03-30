<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 15:58
 */

namespace Flight2wwu\Common;

/**
 * Class BaseController
 * Controllers must extends
 * @package Flight2wwu\Common
 */
abstract class BaseController
{
    /**
     * @return \flight\net\Request
     */
    public static function getRequest()
    {
        return $req = \Flight::request();
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    public static function getInput($name, $default = null)
    {
        $req = self::getRequest();
        if (isset($req->data[$name])) {
            return $req->data[$name];
        }
        if (isset($req->query[$name])) {
            return $req->query[$name];
        }
        return $default;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    public static function getGet($name, $default = null)
    {
        $req = self::getRequest();
        if (isset($req->query[$name])) {
            return $req->query[$name];
        }
        return $default;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    public static function getPost($name, $default = null)
    {
        $req = self::getRequest();
        if (isset($req->data[$name])) {
            return $req->data[$name];
        }
        return $default;
    }

    /**
     * Get inputs array by name list.
     *
     * @param array $namelist
     * @param $default
     * @return array
     */
    public static function getArrayInput(array $namelist, $default = null)
    {
        $out = [];
        foreach ($namelist as $n) {
            $v = self::getInput($n, $default);
            $out[$n] = $v;
        }
        return $out;
    }

    /**
     * Check value.
     *
     * @param $val
     * @param string $type
     * @param bool $throws
     * @return bool
     * @throws \Exception
     */
    public static function checkExists($val, $type = null, $throws = true)
    {
        $pass = true;
        $msg = '';
        if (is_null($val)) {
            $pass = false;
            $msg = 'Value is null!';
        } elseif ($type) {
            if (gettype($val) != $type) {
                $pass = false;
                $msg = 'Type does not match!';
            }
        }
        if ($throws) {
            if (!$pass) {
                throw new \Exception($msg);
            }
        }
        return $pass;
    }

    /**
     * params method1, method2, ...
     * @return bool
     */
    public static function checkMethod()
    {
        $md = self::getRequest()->method;
        $methods = func_get_args();
        foreach ($methods as $m) {
            if ($md == strtoupper($m)) {
                return true;
            }
        }
        return false;
    }

    /**
     * default header
     */
    public static function defaultHeader()
    {
        header('Cache-Control: no-cache');
    }
} 