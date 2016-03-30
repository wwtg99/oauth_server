<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/6
 * Time: 18:00
 */

namespace Flight2wwu\Common;

/**
 * Interface ServiceProvider
 * @package Flight2wwu\Common
 */
interface ServiceProvider
{
    /**
     * Called after register.
     *
     * @return void
     */
    public function register();

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot();
} 