<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/9
 * Time: 16:08
 */

namespace Flight2wwu\Component\Log;

interface ILog
{
    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function debug($msg, array $context = array());

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function info($msg, array $context = array());

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function notice($msg, array $context = array());

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function warning($msg, array $context = array());

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function error($msg, array $context = array());

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function critical($msg, array $context = array());

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function alert($msg, array $context = array());

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function emergency($msg, array $context = array());

    /**
     * @param $name
     * @return mixed
     */
    public function getLogger($name);

    /**
     * @param $name
     * @return ILog
     */
    public function changeLogger($name);
} 