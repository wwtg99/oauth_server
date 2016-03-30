<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 10:53
 */

namespace Flight2wwu\Component\Auth;


class AuthKey
{

    private $auth;

    /**
     * AuthKey constructor.
     * @param int $auth
     */
    public function __construct($auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function access($method = 'GET')
    {
        switch (strtoupper($method)) {
            case 'POST': $v = self::isEnabled($this->auth, 2); break;
            case 'GET':
            default: $v = self::isEnabled($this->auth, 1); break;
        }
        return $v;
    }

    /**
     * @return bool
     */
    public function allowed()
    {
        return $this->auth != 0;
    }

    /**
     * @param int $val
     * @param int $obj
     * @return bool
     */
    public static function isEnabled($val, $obj)
    {
        return ($val & $obj) == $obj;
    }

    /**
     * @param int $val
     * @param int $obj
     * @return bool
     */
    public static function isNotEnabled($val, $obj)
    {
        return ($val & $obj) == 0;
    }

    /**
     * @param int $val
     * @param int $obj
     * @return bool
     */
    public static function isOnlyEnabled($val, $obj)
    {
        return $val == $obj;
    }

    /**
     * @param int $val
     * @param int $obj
     * @return int
     */
    public static function enable($val, $obj)
    {
        return ($val | $obj);
    }

    /**
     * @param int $val
     * @param int $obj
     * @return int
     */
    public static function disable($val, $obj)
    {
        return ($val & ~$obj);
    }
}