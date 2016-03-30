<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 11:59
 */

namespace Flight2wwu\Component\Log;

use Flight2wwu\Common\ServiceProvider;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class Monolog implements ILog, ServiceProvider
{

    /**
     * @var array
     */
    private $loggers = [];

    /**
     * @var string
     */
    private $current = 'main';

    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {
        $configs = \Flight::get('log');
        foreach ($configs as $d => $con) {
            $this->registerLogger($d, $con);
        }
        $this->current = 'main';
    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {

    }

    function __construct()
    {

    }

    /**
     * @param $domain
     * @param array $config
     * @return mixed
     */
    public function registerLogger($domain, array $config)
    {
        if (!array_key_exists($domain, $this->loggers)) {
            $logger = new Logger($domain);
            $level = array_key_exists('level', $config) ? $config['level'] : '';
            $level = self::getLevel($level);
            $name = array_key_exists('title', $config) ? $config['title'] : "$domain.log";
            $max = array_key_exists('max_logfile', $config) ? $config['max_logfile'] : 10;
            $handler = new RotatingFileHandler(LOG . $name, $max, $level, true, 0777);
            $logger->pushHandler($handler);
            $this->loggers[$domain] = $logger;
            $this->current = $domain;
        }
        return $this->loggers[$domain];
    }

    /**
     * @param string $domain
     * @return Logger|array
     */
    public function getLogger($domain = 'main')
    {
        if (is_null($domain)) {
            return $this->loggers;
        }
        if (!array_key_exists($domain, $this->loggers)) {
            $domain = 'main';
        }
        return $this->loggers[$domain];
    }

    /**
     * @param $name
     * @return $this
     */
    public function changeLogger($name = 'main')
    {
        $this->current = $name;
        if (!array_key_exists($this->current, $this->loggers)) {
            $this->current = 'main';
        }
        return $this;
    }

    /**
     * @param string $domain
     * @return string
     */
    public function setCurrentLogger($domain = 'main')
    {
        $this->current = $domain;
        if (!array_key_exists($this->current, $this->loggers)) {
            $this->current = 'main';
        }
        return $this->current;
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function debug($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addDebug($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function info($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addInfo($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function notice($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addNotice($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function warning($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addWarning($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function error($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addError($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function critical($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addCritical($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function alert($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addAlert($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function emergency($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addEmergency($msg, $context);
    }

    /**
     * @param string $level
     * @return int
     */
    public static function getLevel($level) {
        $l = strtoupper($level);
        switch ($l) {
            case 'DEBUG': return Logger::DEBUG;
            case 'INFO': return Logger::INFO;
            case 'NOTICE': return Logger::NOTICE;
            case 'WARNING': return Logger::WARNING;
            case 'ERROR': return Logger::ERROR;
            case 'CRITICAL': return Logger::CRITICAL;
            case 'ALERT': return Logger::ALERT;
            case 'EMERGENCY': return Logger::EMERGENCY;
            default: return Logger::ERROR;
        }
    }
} 