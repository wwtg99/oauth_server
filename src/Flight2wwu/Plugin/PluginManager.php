<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 9:40
 */

namespace Flight2wwu\Plugin;


class PluginManager {

    /**
     * @var PluginManager
     */
    private static $instance = null;

    /**
     * @var string
     */
    private static $conf;

    /**
     * @var array
     */
    private $plugins = [];

    /**
     * @var array
     */
    private $enables = [];


    /**
     * @param array $plugins
     */
    private function __construct($plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * @return PluginManager
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param string $conf
     * @return PluginManager
     */
    public static function loadConfig($conf)
    {
        self::$conf = $conf;
        $f = file_get_contents($conf);
        self::$instance = new PluginManager(json_decode($f, true));
        foreach (self::$instance->plugins as $id => $p) {
            if ($p['enabled']) {
                self::$instance->enable($id);
            }
        }
        return self::$instance;
    }

    /**
     * @param $id
     * @return null|array
     */
    public function getPluginConfig($id = null)
    {
        if (is_null($id)) {
            return $this->plugins;
        } else {
            if (array_key_exists($id, $this->plugins)) {
                return $this->plugins[$id];
            }
        }
        return null;
    }

    /**
     * @param $id
     * @param array $config
     */
    public function setPluginConfig($id, array $config)
    {
        if (!is_null($id) && $config) {
            $this->plugins[$id] = $config;
        }
    }

    /**
     * @param $server_name
     * @return null|IPlugin
     */
    public function getPlugin($server_name)
    {
        if (array_key_exists($server_name, $this->enables)) {
            return $this->enables[$server_name][1];
        }
        return null;
    }

    /**
     * @param $id
     */
    public function enable($id)
    {
        $p = $this->getPluginConfig($id);
        $name = $p['server_name'];
        $cls = $p['class_name'];
        $nm = $p['name'];
        try {
            $ins = new \ReflectionClass($cls);
            $ins = $ins->newInstance();
            if ($ins instanceof IPlugin) {
                $this->enables[$name] = [$id, $ins];
                $p['enabled'] = true;
                $this->setPluginConfig($id, $p);
            }
        } catch (\Exception $e) {
            getLog()->warning("unable to start plugin $nm");
        }
    }

    /**
     * @param $id
     */
    public function disable($id)
    {
        $p = $this->getPluginConfig($id);
        $name = $p['server_name'];
        $p['enabled'] = false;
        $this->setPluginConfig($id, $p);
        if (array_key_exists($name, $this->enables)) {
            if ($this->enables[$name][0] == $id) {
                unset($this->enables[$name]);
            }
        }
    }

    /**
     * Temporary disable server
     *
     * @param $server_name
     */
    public function disable_server($server_name)
    {
        if (array_key_exists($server_name, $this->enables)) {
            unset($this->enables[$server_name]);
        }
    }

    /**
     * List enabled plugin id
     *
     * @return array
     */
    public function list_enables()
    {
        $enables = [];
        foreach ($this->enables as $sname => $server) {
            array_push($enables, $server[0]);
        }
        return $enables;
    }

    /**
     * Write config file
     */
    public function writeConfig()
    {
        if (self::$conf) {
            file_put_contents(self::$conf, json_encode($this->plugins, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        }
    }
} 