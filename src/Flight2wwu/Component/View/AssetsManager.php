<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2015/12/14 0014
 * Time: 下午 9:13
 */

namespace Flight2wwu\Component\View;

use Flight2wwu\Common\ServiceProvider;
use Flight2wwu\Component\Utils\FormatUtils;

class AssetsManager implements ServiceProvider
{
    /**
     * @var array
     */
    private $libs = [];

    /**
     * @var array
     */
    private $css = [];

    /**
     * @var array
     */
    private $js = [];

    /**
     * @var string
     */
    private $default_resource = '';


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
        $conf = \Flight::get('assets');
        if (isset($conf['lib_conf'])) {
            $conf = $conf['lib_conf'];
            $conf = include "$conf";
            $this->loadConfig($conf);
        }
    }

    /**
     * @param array $conf
     */
    public function loadConfig($conf)
    {
        if (is_array($conf)) {
            $this->libs = $conf;
            if (array_key_exists('global_pre', $this->libs)) {
                $this->addLibrary($this->libs['global_pre']);
            }
            if (array_key_exists('default_resource', $this->libs)) {
                $this->default_resource = $this->libs['default_resource'];
            }
        }
    }

    /**
     * @param string|array $lib
     */
    public function addLibrary($lib)
    {
        if (is_array($lib)) {
            $prefix = array_key_exists('prefix', $lib) ? $lib['prefix'] : '';
            foreach ($lib as $k => $v) {
                if ($k === 'prefix') {
                    continue;
                }
                if ($k === 'css') {
                    $this->addCss($v, $prefix);
                } elseif ($k === 'js') {
                    $this->addJs($v, $prefix);
                } elseif ($v != $lib) {
                    $this->addLibrary($v);
                }
            }
        } else {
            if (array_key_exists($lib, $this->libs)) {
                $this->addLibrary($this->libs[$lib]);
            }
        }
    }

    /**
     * @param string|array $css
     * @param string $prefix
     */
    public function addCss($css, $prefix = '')
    {
        if (is_array($css)) {
            foreach ($css as $c) {
                $this->addCss($c, $prefix);
            }
        } else {
            $f = FormatUtils::formatPath($prefix) . DIRECTORY_SEPARATOR . $css;
            if (!in_array($f, $this->css)) {
                array_push($this->css, $f);
            }
        }
    }

    /**
     * @param string|array $js
     * @param string $prefix
     */
    public function addJs($js, $prefix = '')
    {
        if (is_array($js)) {
            foreach ($js as $c) {
                $this->addJs($c, $prefix);
            }
        } else {
            $f = FormatUtils::formatPath($prefix) . DIRECTORY_SEPARATOR . $js;
            if (!in_array($f, $this->js)) {
                array_push($this->js, $f);
            }
        }
    }

    /**
     * @return string
     */
    public function renderCss()
    {
        if (array_key_exists('global_post', $this->libs)) {
            $this->addLibrary('global_post');
        }
        $out = [];
        foreach ($this->css as $css) {
            $f = "<link rel='stylesheet' href='$css'>";
            array_push($out, $f);
        }
        return implode("\n", $out) . "\n";
    }

    /**
     * Dump css.
     */
    public function dumpCss()
    {
        echo $this->renderCss();
    }

    /**
     * @return string
     */
    public function renderJs()
    {
        if (array_key_exists('global_post', $this->libs)) {
            $this->addLibrary('global_post');
        }
        $out = [];
        foreach ($this->js as $js) {
            $f = "<script type='text/javascript' src='$js'></script>";
            array_push($out, $f);
        }
        return implode("\n", $out) . "\n";
    }

    /**
     * Dump js.
     */
    public function dumpJs()
    {
        echo $this->renderJs();
    }

    /**
     * @param string $name
     * @param string $prefix
     * @return string
     */
    public function getResource($name, $prefix = '')
    {
        if (!$prefix) {
            $prefix = $this->default_resource;
        }
        return FormatUtils::formatPath($prefix) . DIRECTORY_SEPARATOR . $name;
    }

}