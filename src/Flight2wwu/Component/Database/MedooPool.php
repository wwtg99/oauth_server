<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/8 0008
 * Time: 下午 2:33
 */

namespace Flight2wwu\Component\Database;


use Flight2wwu\Common\ServiceProvider;

class MedooPool extends ADatabasePool implements ServiceProvider
{
    /**
     * @param array $config
     * @param string $name
     * @return MedooPlus
     */
    public function connect(array $config, $name = 'main')
    {
        $db_conf = [
            'database_type' => $config['driver'],
            'database_name' => $config['dbname'],
            'server' => $config['host'],
            'username' => $config['user'],
            'password' => $config['password'],
            'port' => $config['port'],
            'charset' => 'utf8',
        ];
        if (array_key_exists('option', $config)) {
            $db_conf['option'] = $config['option'];
        }
        if (array_key_exists('prefix', $config)) {
            $db_conf['prefix'] = $config['prefix'];
        }
        $database = new MedooPlus($db_conf);
        return $database;
    }

    /**
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function reconnect($name = 'main')
    {
        if (!array_key_exists($name, $this->connections)) {
            $conf = \Flight::get('database');
            if (!array_key_exists($name, $conf)) {
                throw new \Exception("database config $name is not exists");
            }
            $main = $conf[$name];
            $db = $this->connect($main, $name);
            $this->connections[$name] = $db;
            $db->debug = isDebug();
        }
        $this->current = $name;
        return $this;
    }

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
        $this->reconnect();
    }

}