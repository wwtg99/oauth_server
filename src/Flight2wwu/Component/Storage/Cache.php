<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/29
 * Time: 16:05
 */

namespace Flight2wwu\Component\Storage;

use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Adapter\Apcu;
use Desarrolla2\Cache\Adapter\File;
use Flight2wwu\Common\ServiceProvider;
use League\Flysystem\Exception;

class Cache implements ServiceProvider, IAttribute
{

    /**
     * @var \Desarrolla2\Cache\Cache
     */
    private $cache;

    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $conf = \Flight::get('storage');
        if (isset($conf['cache'])) {
            $conf = $conf['cache'];
            if (isset($conf['adapter']) && isset($conf['params'])) {
                $adapter = $conf['adapter'];
                $params = $conf['params'];
                $this->initAdapter($adapter, $params);
            }
        }
    }

    function __construct()
    {

    }

    /**
     * @return \Desarrolla2\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->cache) {
            return $this->cache->get($name);
        }
        return null;
    }

    /**
     * @param string $name
     * @param $val
     * @param int $expire
     * @return IAttribute
     */
    public function set($name, $val, $expire = null)
    {
        if ($this->cache) {
            $this->cache->set($name, $val, $expire);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if ($this->cache) {
            return $this->cache->has($name);
        }
        return false;
    }

    /**
     * @param string $name
     * @return IAttribute
     */
    public function delete($name)
    {
        if ($this->cache) {
            $this->cache->delete($name);
        }
        return $this;
    }

    /**
     * @param string $adapter
     * @param array $params
     * @throws Exception
     */
    private function initAdapter($adapter, array $params)
    {
        switch($adapter) {
            case 'Apcu': $adapter = $this->initApcu($params); break;
            case 'File': $adapter = $this->initFile($params); break;
            default:
                throw new Exception("Adapter $adapter is not supported");
        }
        $this->cache = new \Desarrolla2\Cache\Cache($adapter);
    }

    /**
     * @param array $params
     * @return Apcu
     */
    private function initApcu(array $params)
    {
        $adapter = new Apcu();
        $this->setOption($adapter, $params);
        return $adapter;
    }

    /**
     * @param array $params
     * @return File
     */
    private function initFile(array $params)
    {
        if (array_key_exists('cacheDir', $params)) {
            $cd = $params['cacheDir'];
            unset($params['cacheDir']);
        } else {
            $cd = null;
        }
        if (!file_exists($cd)) {
            mkdir($cd, 0777, true);
        }
        $adapter = new File($cd);
        $this->setOption($adapter, $params);
        return $adapter;
    }

    /**
     * @param AbstractAdapter $adapter
     * @param array $params
     * @return AbstractAdapter
     */
    private function setOption(AbstractAdapter $adapter, array $params)
    {
        foreach ($params as $k => $v) {
            $adapter->setOption($k, $v);
        }
        return $adapter;
    }
} 