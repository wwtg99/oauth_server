<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/16
 * Time: 15:04
 */

namespace App\Controller;


use App\Model\Admin;
use Flight2wwu\Common\BaseController;
use Flight2wwu\Plugin\PluginManager;
use Flight2wwu\Component\Utils\FormatUtils;

class AdminController extends BaseController
{

    public static function plugins()
    {
        $plugins = PluginManager::getInstance()->getPluginConfig();
        $phead = ['name', 'server_name', 'category', 'description', 'enabled'];
        $pl = [];
        foreach ($plugins as $id => $p) {
            $tmp = ['id'=>$id];
            foreach ($phead as $h) {
                $tmp[$h] = $p[$h];
            }
            array_push($pl, $tmp);
        }
        array_unshift($phead, 'id');
        getAssets()->addLibrary('bootstrap-table');
        getView()->render('admin/plugins', ['plugins'=>$pl, 'plugins_head'=>FormatUtils::formatHead($phead)]);
    }

    public static function enable_plugin()
    {
        if (self::checkMethod('POST')) {
            $pid = self::getInput('id');
            PluginManager::getInstance()->enable($pid);
            PluginManager::getInstance()->writeConfig();
            \Flight::json(['enabled'=>$pid]);
        }
        \Flight::redirect('/404');
    }

    public static function disable_plugin()
    {
        if (self::checkMethod('POST')) {
            $pid = self::getInput('id');
            PluginManager::getInstance()->disable($pid);
            PluginManager::getInstance()->writeConfig();
            \Flight::json(['disabled'=>$pid]);
        }
        \Flight::redirect('/404');
    }
} 