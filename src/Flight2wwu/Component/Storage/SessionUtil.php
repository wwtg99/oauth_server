<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 11:20
 */

namespace Flight2wwu\Component\Storage;


use Flight2wwu\Common\ServiceProvider;

class SessionUtil implements ServiceProvider, IAttribute
{

    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @var array
     */
    private $session = [];

    /**
     * @var string
     */
    private $prefix;

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
        $enabled = array_key_exists('session', $st) ? $st['session'] : '';
        if ($enabled) {
            $this->enabled = true;
            $this->start();
            $this->session = & $_SESSION;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->session[$this->prefix . $name][0];
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
            $this->session[$this->prefix . $name] = [$val, $this->calExpireSeconds($expire)];
        }
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if ($this->enabled) {
            $name = $this->prefix . $name;
            if (isset($this->session[$name])) {
                if (!$this->session[$name][1] || time() <= $this->session[$name][1]) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function delete($name)
    {
        if ($this->has($name)) {
            unset($this->session[$this->prefix . $name]);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function start()
    {
        session_start();
        return $this;
    }

    /**
     * @return $this
     */
    public function destroy()
    {
        session_destroy();
        return $this;
    }

    /**
     * @return $this
     */
    public function close()
    {
        session_write_close();
        return $this;
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