<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 5:32
 */

namespace Flight2wwu\Component\Database;


abstract class ADatabasePool implements IDatabasePool
{

    /**
     * @var array
     */
    protected $connections = [];

    /**
     * @var string
     */
    protected $current = '';

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function getConnection($name = null)
    {
        if (is_null($name)) {
            $name = $this->current;
        }
        if (count($this->connections) <= 0) {
            throw new \Exception('no connections yet');
        }
        if (!array_key_exists($name, $this->connections)) {
            $this->current = key($this->connections);
        }
        return $this->connections[$this->current];
    }

}