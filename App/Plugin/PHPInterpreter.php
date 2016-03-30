<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 11:22
 */

namespace App\Plugin;


use Flight2wwu\Plugin\CommandPlugin;

class PHPInterpreter extends CommandPlugin
{
    function __construct()
    {
        $this->async = false;
        $this->cmd = 'php';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        $re = $this->exec('-v');
        return $re;
    }

    /**
     * Run with parameters
     *
     * @return mixed
     */
    public function run()
    {

    }

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

} 