<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 16:10
 */

//define global functions here

/**
 * @param $name
 * @return \Flight2wwu\Plugin\IPlugin
 * @throws Exception
 */
function getPlugin($name)
{
    $p = \Flight2wwu\Plugin\PluginManager::getInstance()->getPlugin($name);
    if (is_null($p) || !($p instanceof \Flight2wwu\Plugin\IPlugin)) {
        throw new Exception("plugin $name not exists");
    }
    return $p;
}

/**
 * @return \Flight2wwu\Component\Database\PdoDB|\Flight2wwu\Component\Database\MedooDB
 */
function getDB()
{
    return Flight::DB();
}

/**
 * @return \Flight2wwu\Component\Log\ILog
 */
function getLog()
{
    $log = Flight::Log();
    if (!$log) {
        $log = new \Flight2wwu\Component\Log\SdoutLog();
    }
    return $log;
}

/**
 * @return \Flight2wwu\Component\View\AbstractView
 */
function getView()
{
    return Flight::View();
}

/**
 * @return \Flight2wwu\Component\Storage\OldValue
 */
function getOValue()
{
    return Flight::Value();
}

/**
 * @param string $name
 * @param string $def
 * @return mixed
 */
function getOld($name, $def = '')
{
    $v = getOValue();
    $c = new \Flight2wwu\Component\Storage\Collection($v->getOlds());
    if ($c->has($name)) {
        return $v->getOld($name, $def);
    } else {
        return $v->getOldOnce($name, $def);
    }
}

/**
 * @return \Flight2wwu\Component\View\AssetsManager
 */
function getAssets()
{
    return Flight::Assets();
}

/**
 * @return \Flight2wwu\Component\Auth\IAuth
 */
function getAuth()
{
    return Flight::Auth();
}

/**
 * @param string $key
 * @return array|string
 */
function getUser($key = '')
{
    if ($key) {
        $user = Flight::Auth()->getUser();
        if ($user && array_key_exists($key, $user)) {
            return $user[$key];
        } else {
            return '';
        }
    } else {
        return Flight::Auth()->getUser();
    }
}

/**
 * @return \Flight2wwu\Component\Storage\Cache
 */
function getCache()
{
    return Flight::Cache();
}

/**
 * @return \Flight2wwu\Component\Storage\SessionUtil
 */
function getSession()
{
    return Flight::Session();
}

/**
 * @return \Flight2wwu\Component\Storage\CookieUtil
 */
function getCookie()
{
    return Flight::Cookie();
}

/**
 * @return \Flight2wwu\Component\Utils\Mail
 */
function getMailer()
{
    return Flight::Mail();
}

/**
 * @return bool
 */
function isDebug()
{
    $d = Flight::get('debug');
    if ($d) {
        return true;
    } else {
        return false;
    }
}

/**
 * Translate
 *
 * @param string $key
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 * @return string
 */
function T($key, $parameters = [], $domain = 'messages', $locale = null)
{
    $trans = Flight::Locale();
    return $trans->trans($key, $parameters, false, $domain, $locale);
}

/**
 * Translate case sensitive
 *
 * @param $key
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 * @return string
 */
function TI($key, $parameters = [], $domain = 'messages', $locale = null)
{
    $trans = Flight::Locale();
    return $trans->trans($key, $parameters, true, $domain, $locale);
}

/**
 * Translate and print
 *
 * @param string $key
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 */
function TP($key, $parameters = [], $domain = 'messages', $locale = null)
{
    echo T($key, $parameters, $domain, $locale);
}

/**
 * Translate case sensitive and print
 *
 * @param string $key
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 */
function TIP($key, $parameters = [], $domain = 'messages', $locale = null)
{
    echo TI($key, $parameters, $domain, $locale);
}


/**
 * Translate array
 *
 * @param array $array
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 * @return array
 */
function TA(array $array, $parameters = [], $domain = 'messages', $locale = null)
{
    $trans = Flight::Locale();
    return $trans->transArray($array, $parameters, false, $domain, $locale);
}

/**
 * Translate array case sensitive
 *
 * @param array $array
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 * @return array
 */
function TAI(array $array, $parameters = [], $domain = 'messages', $locale = null)
{
    $trans = Flight::Locale();
    return $trans->transArray($array, $parameters, true, $domain, $locale);
}
