<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/9
 * Time: 16:10
 */

namespace Flight2wwu\Component\Log;

class SdoutLog implements ILog
{
    /**
     * @param $name
     * @return mixed
     */
    public function getLogger($name)
    {
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function changeLogger($name)
    {
        return $this;
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function debug($msg, array $context = array())
    {
        $this->formatLog('DEBUG', $msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function info($msg, array $context = array())
    {
        $this->formatLog('INFO', $msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function notice($msg, array $context = array())
    {
        $this->formatLog('NOTICE', $msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function warning($msg, array $context = array())
    {
        $this->formatLog('WARNING', $msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function error($msg, array $context = array())
    {
        $this->formatLog('ERROR', $msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function critical($msg, array $context = array())
    {
        $this->formatLog('CRITICAL', $msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function alert($msg, array $context = array())
    {
        $this->formatLog('ALERT', $msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function emergency($msg, array $context = array())
    {
        $this->formatLog('EMERGENCY', $msg, $context);
    }

    /**
     * @param string $level
     * @param string $msg
     * @param array $context
     * @return string
     */
    private function formatLog($level, $msg, array $context = array())
    {
        $time = strftime('%Y-%m-%d %H:%M:%S');
        $l = "[$level] $time $msg  " . var_export($context, true) . "\n";
        echo $l;
    }

} 