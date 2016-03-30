<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 17:40
 */

namespace Flight2wwu\Component\Translation;


interface ITranslator
{

    /**
     * @param string $locale
     * @return ITranslator
     */
    public function setLocale($locale);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $key
     * @param array $parameters
     * @param bool $case_sensitive
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function trans($key, $parameters = [], $case_sensitive = false, $domain = 'messages', $locale = null);

    /**
     * @param array $array
     * @param array $parameters
     * @param bool $case_sensitive
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function transArray(array $array, $parameters = [], $case_sensitive = false, $domain = 'messages', $locale = null);

    /**
     * @return array
     */
    public function getAll();
}