<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 10:43
 */

namespace Flight2wwu\Component\View;

use Flight2wwu\Common\ServiceProvider;

abstract class AbstractView implements ServiceProvider
{

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

    }

    /**
     * @param $template
     * @param array $data
     */
    abstract public function render($template, array $data = null);

} 