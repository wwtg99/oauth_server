<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 11:36
 */

namespace Flight2wwu\Component\Storage;


use Flight2wwu\Common\ServiceProvider;

class CookieUtil implements ServiceProvider, IAttribute
{

    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @var array
     */
    private $cookies = [];

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $path = '/';

    /**
     * @var string
     */
    private $domain = null;

    /**
     * @return mixed
     */
    public function register()
    {

    }

    /**
     * @return mixed
     */
    public function boot()
    {
        $st = \Flight::get('storage');
        $this->prefix = array_key_exists('prefix', $st) ? $st['prefix'] . '_' : '';
        $this->path = array_key_exists('cookie_path', $st) ? $st['cookie_path'] : '/';
        $this->domain = array_key_exists('domain', $st) ? $st['domain'] : null;
        $enabled = array_key_exists('cookie', $st) ? $st['cookie'] : '';
        if ($enabled) {
            $this->enabled = true;
            $this->cookies = & $_COOKIE;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->cookies[$this->prefix . $name];
        }
        return null;
    }

    /**
     * @param string $name
     * @param $val
     * @param int $expire
     * @return $this
     */
    public function set($name, $val, $expire = 0)
    {
        if ($this->enabled) {
            setcookie($this->prefix . $name, $val, $this->calExpireSeconds($expire), $this->path, $this->domain);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function delete($name)
    {
        if ($this->has($name)) {
            $this->set($name, null);
        }
        return $this;
    }

    /**
     * Store for one year.
     *
     * @param string $name
     * @param $val
     */
    public function forever($name, $val)
    {
        if ($this->enabled) {
            $this->set($name, $val, 60 * 24 * 365);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if ($this->enabled) {
            return isset($this->cookies[$this->prefix . $name]);
        }
        return false;
    }

    /**
     * @param int $sec
     * @return int
     */
    private function calExpireSeconds($sec)
    {
        return ($sec > 0) ? time() + $sec : 0;
    }

}