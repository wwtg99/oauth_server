<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 4:59
 */

namespace Flight2wwu\Component\Database;


interface IDatabasePool
{

    /**
     * @param array $config
     * @param string $name
     * @return $this
     */
    public function connect(array $config, $name = 'main');

    /**
     * @param string $name
     * @return $this
     */
    public function reconnect($name = 'main');

    /**
     * @param string $name
     * @return mixed
     */
    public function getConnection($name = null);
}