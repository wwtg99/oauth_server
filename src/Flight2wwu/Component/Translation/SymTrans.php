<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 19:19
 */

namespace Flight2wwu\Component\Translation;

use Flight2wwu\Common\ServiceProvider;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

class SymTrans implements ServiceProvider, ITranslator
{

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var string
     */
    private $path;

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
        $locale = getOValue()->getOld('language');
        if (!$locale) {
            $locale = \Flight::get('language');
        }
        $this->translator = new Translator($locale);
        $this->translator->addLoader('array', new ArrayLoader());
        $this->setLocale($locale);
    }

    function __construct()
    {
        $this->path = CONFIG . 'lang';
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = rtrim(trim($path), DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
        $this->addResource($locale, 'messages');
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * @param string $key
     * @param array $parameters
     * @param bool $case_insensitive
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function trans($key, $parameters = [], $case_insensitive = false, $domain = 'messages', $locale = null)
    {
        $v = $this->translator->trans($key, $parameters, $domain, $locale);
        if ($case_insensitive) {
            if ($v == $key) {
                $lkey = strtolower($key);
                $v = $this->translator->trans($lkey, $parameters, $domain, $locale);
                if ($v == $lkey) {
                    return $key;
                }
            }
        }
        return $v;
    }

    /**
     * @param array $array
     * @param array $parameters
     * @param bool $case_sensitive
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function transArray(array $array, $parameters = [], $case_sensitive = false, $domain = 'messages', $locale = null)
    {
        $out = [];
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $out[$k] = $this->transArray($v, $parameters, $case_sensitive, $domain, $locale);
            } else {
                $out[$k] = $this->trans($v, $parameters, $case_sensitive, $domain, $locale);
            }
        }
        return $out;
    }

    /**
     * @param string $locale
     * @return array
     */
    public function getAll($locale = null)
    {
        return $this->translator->getMessages($locale);
    }

    /**
     * @param string $locale
     * @param string $domain
     */
    public function addResource($locale, $domain = 'messages')
    {
        $resource = $domain;
        if (substr($domain, -4, 4) != '.php') {
            $resource .= '.php';
        } else {
            $domain = substr($domain, 0, strlen($domain) - 4);
        }
        $file = implode(DIRECTORY_SEPARATOR, [$this->path, $locale, $resource]);
        if (file_exists($file)) {
            $re = include "$file";
            $this->translator->addResource('array', $re, $locale, $domain);
        }
    }

} 