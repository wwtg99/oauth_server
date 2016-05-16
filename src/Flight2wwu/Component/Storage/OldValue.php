<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 13:25
 */

namespace Flight2wwu\Component\Storage;


use Flight2wwu\Common\ServiceProvider;

class OldValue implements ServiceProvider
{

    const OLD_KEY = 'OLD_VALUE';
    const OLD_ONCE_KEY = 'OLD_ONCE_VALUE';

    /**
     * @var IAttribute
     */
    private $storage;

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
        $conf = \Flight::get('storage');
        $this->loadConfig($conf);
    }

    /**
     * @param array $conf
     */
    public function loadConfig(array $conf)
    {
        if (isset($conf['old_value'])) {
            $method = $conf['old_value'];
            switch(strtolower($method)) {
                case 'cache': $this->storage = getCache(); break;
                case 'session':
                default: $this->storage = getSession(); break;
            }
            $this->checkOld();
            $this->checkOldOnce();
        }
    }

    /**
     * @param string $name
     * @param $val
     * @return $this
     */
    public function addOld($name, $val)
    {
        if ($this->storage) {
            $old = $this->storage->get(OldValue::OLD_KEY);
            $old[$name] = $val;
            $this->storage->set(OldValue::OLD_KEY, $old);
        }
        return $this;
    }

    /**
     * @param array $vals
     * @return $this
     */
    public function addOlds(array $vals)
    {
        foreach ($vals as $n => $v) {
            $this->addOld($n, $v);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param $val
     * @return $this
     */
    public function addOldOnce($name, $val)
    {
        if ($this->storage) {
            $old_once = $this->storage->get(OldValue::OLD_ONCE_KEY);
            $old_once[$name] = $val;
            $this->storage->set(OldValue::OLD_ONCE_KEY, $old_once);
        }
        return $this;
    }

    /**
     * @param array $vals
     * @return $this
     */
    public function addOldsOnce(array $vals)
    {
        foreach ($vals as $n => $v) {
            $this->addOldOnce($n, $v);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param string $def
     * @return string
     */
    public function getOld($name, $def = '')
    {
        if ($this->storage) {
            $old = $this->storage->get(OldValue::OLD_KEY);
            $old = new Collection($old);
            if ($old->has($name)) {
                return $old->get($name);
            }
        }
        return $def;
    }

    /**
     * @return array
     */
    public function getOlds()
    {
        if ($this->storage) {
            return $this->storage->get(OldValue::OLD_KEY);
        }
        return [];
    }

    /**
     * @param string $name
     * @return $this
     */
    public function deleteOld($name)
    {
        if ($this->storage) {
            $old = $this->storage->get(OldValue::OLD_KEY);
            unset($old[$name]);
            $this->storage->set(OldValue::OLD_KEY, $old);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function clearOld()
    {
        if ($this->storage) {
            $this->storage->set(OldValue::OLD_KEY, []);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param string $def
     * @return string
     */
    public function getOldOnce($name, $def = '')
    {
        if ($this->storage) {
            $old_once = $this->storage->get(OldValue::OLD_ONCE_KEY);
            $old_once = new Collection($old_once);
            if ($old_once->has($name)) {
                $v = $old_once->get($name);
                $old_once->delete($name);
                $this->storage->set(OldValue::OLD_ONCE_KEY, $old_once->get());
                return $v;
            }
        }
        return $def;
    }

    /**
     * @return $this
     */
    public function clearOldOnce()
    {
        if ($this->storage) {
            $this->storage->set(OldValue::OLD_ONCE_KEY, []);
        }
        return $this;
    }

    /**
     * @return $this
     */
    private function checkOld()
    {
        if (!$this->storage->has(OldValue::OLD_KEY)) {
            $this->storage->set(OldValue::OLD_KEY, []);
        }
        return $this;
    }

    /**
     * @return $this
     */
    private function checkOldOnce()
    {
        if (!$this->storage->has(OldValue::OLD_ONCE_KEY)) {
            $this->storage->set(OldValue::OLD_ONCE_KEY, []);
        }
        return $this;
    }

}