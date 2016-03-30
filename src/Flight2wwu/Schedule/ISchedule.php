<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 12:05
 */

namespace Flight2wwu\Schedule;


interface ISchedule {

    /**
     * Call this function each time
     *
     * @return mixed
     */
    public function run();

    /**
     * Register schedule
     *
     * @param array $conf
     * @return mixed
     */
    public function register($conf);

} 