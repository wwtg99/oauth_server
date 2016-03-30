<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 13:41
 */

namespace Flight2wwu\Component\Storage;


interface IAttribute
{

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param string $name
     * @param $val
     * @param int $expire
     * @return IAttribute
     */
    public function set($name, $val, $expire = 0);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param string $name
     * @return IAttribute
     */
    public function delete($name);
}