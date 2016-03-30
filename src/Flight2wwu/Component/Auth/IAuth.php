<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 10:37
 */

namespace Flight2wwu\Component\Auth;


interface IAuth
{

    /**
     * @param array $user
     * @param bool $cookie
     * @return bool
     */
    public function attempt(array $user, $cookie = true);

    /**
     * @param array $user
     * @param int|null $expires: expire minutes
     * @return mixed
     */
    public function login(array $user, $expires = null);

    /**
     * @return bool
     */
    public function isLogin();

    /**
     * @return mixed
     */
    public function logout();

    /**
     * @return array
     */
    public function getUser();

    /**
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role);

    /**
     * @param string $object
     * @return AuthKey
     */
    public function getAuth($object);

    /**
     * @param string $path
     * @return AuthKey
     */
    public function accessPath($path);

    /**
     * @return bool
     */
    public function isSuperuser();
}