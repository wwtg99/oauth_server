<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/3
 * Time: 15:45
 */

namespace Flight2wwu\Plugin;

/**
 * Class ARunPlugin
 * @package Flight2wwu\Plugin
 */
abstract class ARunPlugin implements IPlugin
{
    /**
     * Call method by the first parameter, and other parameters in an array as arguments
     *
     * @return mixed|null
     */
    public function run()
    {
        $agv = func_get_args();
        if (count($agv) >= 1) {
            $func_name = $agv[0];
            $func_args = array_splice($agv, 0, 1);
            $rc = new \ReflectionClass($this);
            if ($rc->hasMethod($func_name)) {
                $re = $rc->getMethod($func_name)->invoke($this, $func_args);
                return $re;
            }
        }
        return null;
    }

}