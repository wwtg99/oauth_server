<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/8 0008
 * Time: 下午 8:44
 */

namespace Flight2wwu\Component\View;


use Components\Comp\AlertView;
use Components\Comp\ListView;
use Components\Comp\StepView;
use Flight2wwu\Component\Utils\FormatUtils;

class TwigView extends AbstractView
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $view = \Flight::get('view');
        $this->loadConfig($view);
        $this->addExtensions();
    }

    /**
     * @param array $conf
     */
    public function loadConfig($conf)
    {
        $dir = isset($conf['view_dir']) ? FormatUtils::formatPath($conf['view_dir']) : '';
        $loader = new \Twig_Loader_Filesystem($dir);
        if (isDebug()) {
            $conf['debug'] = true;
            $this->twig = new \Twig_Environment($loader, $conf);
            $this->twig->addExtension(new \Twig_Extension_Debug());
        } else {
            $this->twig = new \Twig_Environment($loader, $conf);
        }
    }

    /**
     * @param $template
     * @param array $data
     */
    public function render($template, array $data = [])
    {
        echo $this->twig->render($this->getTemplate($template), $data);
    }

    /**
     * @param $template
     * @return string
     */
    private function getTemplate($template)
    {
        $template = trim($template);
        $dpos = strrpos($template, '.');
        if ($dpos === false) {
            return $template . '.twig';
        }
        $suf = substr($template, $dpos);
        if (!in_array($suf, ['.html', '.php', '.twig'])) {
            return $template . '.twig';
        }
        return $template;
    }

    private function addExtensions()
    {
        $func = new \Twig_SimpleFunction('isDebug', function() {
            return isDebug();
        });
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('debugbarHead', function() {
            $debugbar = \Flight::get('debugbar');
            if ($debugbar) {
                $debugRender = $debugbar->getJavascriptRenderer();
                $debugRender->setBaseUrl('/asserts/debugbar');
                return $debugRender->renderHead();
            }
            return '';
        }, ['is_safe'=>['html']]);
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('renderDebugbar', function() {
            $debugbar = \Flight::get('debugbar');
            if ($debugbar) {
                $debugRender = $debugbar->getJavascriptRenderer();
                return $debugRender->render();
            }
            return '';
        }, ['is_safe'=>['html']]);
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('renderAsserts', function($addlib = []) {
            $ass = getAssets();
            if ($addlib) {
                $ass->addLibrary($addlib);
            }
            return $ass->renderCss() . $ass->renderJs();
        }, ['is_safe'=>['html']]);
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('isLogin', function() {
            return getAuth()->isLogin();
        });
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('isSuperuser', function() {
            return getAuth()->isSuperuser();
        });
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('hasRole', function($role) {
            return getAuth()->hasRole($role);
        });
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('getConfig', function($name) {
            return \Flight::get($name);
        });
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('getUser', function() {
            return getAuth()->getUser();
        });
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('old', function($name, $def = '') {
            return getOld($name, $def);
        });
        $this->twig->addFunction($func);
        $func = new \Twig_SimpleFunction('component', function($name, $param = []) {
            switch ($name) {
                case 'alert':
                    $level = isset($param['level']) ? $param['level'] : 'info';
                    $comp = new AlertView($level);
                    break;
                case 'list':
                    $cols = isset($param['columns']) ? $param['columns'] : 1;
                    $align = isset($param['align']) ? $param['align'] : 'left';
                    $comp = new ListView($cols, $align);
                    break;
                case 'step':
                    $steps = $param['steps'];
                    $cur = isset($param['current']) ? $param['current'] : 0;
                    $comp = new StepView($steps, $cur);
                    break;
            }
            if (isset($comp)) {
                $data = isset($param['data']) ? $param['data'] : [];
                $re = $comp->view($data);
                return $re;
            }
            return '';
        }, ['is_safe'=>['html']]);
        $this->twig->addFunction($func);
        $filter = new \Twig_SimpleFilter('T', function($val, $param = [], $domain = 'messages', $locale = null) {
            return T($val, $param, $domain, $locale);
        });
        $this->twig->addFilter($filter);
        $filter = new \Twig_SimpleFilter('TI', function($val, $param = [], $domain = 'messages', $locale = null) {
            return TI($val, $param, $domain, $locale);
        });
        $this->twig->addFilter($filter);
        $filter = new \Twig_SimpleFilter('TA', function(array $val, $param = [], $domain = 'messages', $locale = null) {
            return TA($val, $param, $domain, $locale);
        });
        $this->twig->addFilter($filter);
        $filter = new \Twig_SimpleFilter('TAI', function(array $val, $param = [], $domain = 'messages', $locale = null) {
            return TAI($val, $param, $domain, $locale);
        });
        $this->twig->addFilter($filter);
        //json_encode
        $filter = new \Twig_SimpleFilter('J', function($obj, $options = JSON_UNESCAPED_UNICODE) {
            return json_encode($obj, $options);
        }, ['is_safe'=>['html']]);
        $this->twig->addFilter($filter);
        //markdown
        $filter = new \Twig_SimpleFilter('M', function($text) {
            $md = new \Parsedown();
            return $md->text($text);
        }, ['is_safe'=>['html']]);
        $this->twig->addFilter($filter);
    }
}