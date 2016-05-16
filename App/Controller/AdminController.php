<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/16
 * Time: 15:04
 */

namespace App\Controller;


use App\Model\Admin;
use App\Model\Message;
use Flight2wwu\Common\BaseController;
use Flight2wwu\Plugin\PluginManager;
use Flight2wwu\Component\Utils\FormatUtils;

class AdminController extends BaseController
{

    public static function home()
    {
        $dnum = Admin::departmentNum();
        $unum = Admin::userNum();
        $rnum = Admin::roleNum();
        getView()->render('admin/home', ['department_num'=>$dnum, 'user_num'=>$unum, 'role_num'=>$rnum]);
    }

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
        \Flight::redirect('/404', 404);
    }

    public static function disable_plugin()
    {
        if (self::checkMethod('POST')) {
            $pid = self::getInput('id');
            PluginManager::getInstance()->disable($pid);
            PluginManager::getInstance()->writeConfig();
            \Flight::json(['disabled'=>$pid]);
        }
        \Flight::redirect('/404', 404);
    }

    public static function users()
    {
        $uid = self::getInput('user_id');
        if ($uid) {
            $user = Admin::getUser($uid);
            $dep = Admin::getDepartment();
            $roles = Admin::getRole();
            getAssets()->addLibrary(['bootstrap-table', 'bootstrap-select', 'icheck', 'lodash', 'bootstrap-dialog']);
            getView()->render('admin/user_info', ['user_id' => $uid, 'user' => $user, 'departments' => $dep, 'roles' => $roles]);
        } else {
            $users = Admin::getUser();
            $uhead = ['user_id', 'name', 'label', 'email', 'descr', 'department_id', 'department', 'department_descr', 'superuser', 'roles', 'created_at', 'updated_at', 'deleted_at'];
            getAssets()->addLibrary(['bootstrap-table', 'bootstrap-dialog']);
            getView()->render('admin/users', ['users' => FormatUtils::formatTransArray($users), 'users_head' => FormatUtils::formatHead($uhead)]);
        }
    }

    public static function add_user()
    {
        if (self::checkMethod('POST')) {
            $uid = self::getInput('user_id');
            $user = self::getArrayInput(['name', 'label', 'email', 'descr', 'department_id']);
            $roles = self::getInput('roles');
            if ($uid) {
                if (!$roles) {
                    $msg = Message::getMessage(1, 'invalid roles');
                } else {
                    $re = Admin::updateUser($uid, $user, $roles);
                    if ($re) {
                        $msg = Message::getMessage(0, 'update successfully', 'success');
                    } else {
                        $msg = Message::getMessage(3);
                    }
                }
                $url = "/admin/users?user_id=$uid";
            } else {
                if (!$roles) {
                    $msg = Message::getMessage(1, 'invalid roles');
                    $url = '/admin/add_user';
                } else {
                    $re = Admin::createUser($user, $roles);
                    if ($re) {
                        $uid = $re;
                        $url = "/admin/users?user_id=$uid";
                        $msg = Message::getMessage(0, 'create successfully', 'success');
                    } else {
                        $msg = Message::getMessage(1);
                        $url = '/admin/add_user';
                    }
                }
            }
            if ($msg) {
                getOValue()->addOldOnce('admin_msg', $msg);
            }
            \Flight::redirect($url);
        } else {
            $dep = Admin::getDepartment();
            $roles = Admin::getRole();
            getAssets()->addLibrary(['bootstrap-table', 'bootstrap-select', 'icheck', 'lodash']);
            getView()->render('admin/user_info', ['departments' => $dep, 'roles' => $roles]);
        }
    }

    public static function delete_user()
    {
        if (self::checkMethod('POST')) {
            $uid = self::getInput('user_id');
            $del = self::getInput('hard', false);
            $active = self::getInput('active', false);
            if ($del) {
                $re = Admin::deleteUser($uid);
            } else {
                $re = Admin::activeUser($uid, $active);
            }
            \Flight::json(['result'=>$re]);
            return false;
        }
        \Flight::redirect('/404', 404);
        return false;
    }

    public static function reset_pwd()
    {
        if (self::checkMethod('POST')) {
            $uid = self::getInput('user_id');
            $re = Admin::resetPassword($uid);
            \Flight::json(['result'=>$re]);
            return false;
        }
        \Flight::redirect('/404', 404);
        return false;
    }

    public static function roles()
    {
        $rid = self::getInput('role_id');
        if ($rid) {
            $role = Admin::getRole($rid);
            getAssets()->addLibrary(['bootstrap-table']);
            getView()->render('admin/role_info', ['role' => $role, 'role_id'=>$rid]);
        } else {
            $roles = Admin::getRole();
            $uhead = ['role_id', 'name', 'descr', 'created_at', 'updated_at'];
            getAssets()->addLibrary(['bootstrap-table', 'bootstrap-dialog']);
            getView()->render('admin/roles', ['roles' => FormatUtils::formatTransArray($roles), 'roles_head' => FormatUtils::formatHead($uhead)]);
        }
    }

    public static function add_role()
    {
        if (self::checkMethod('POST')) {
            $rid = self::getInput('role_id');
            $name = self::getInput('name');
            $des = self::getInput('descr');
            if (!self::checkExists($name, null, false)) {
                $msg = Message::getMessage(1);
                $url = '/admin/add_role';
            } else {
                if ($rid) {
                    $re = Admin::updateRole($rid, $name, $des);
                    if ($re) {
                        $url = "/admin/roles?role_id=$rid";
                        $msg = Message::getMessage(0, 'update successfully', 'success');
                    } else {
                        $url = "/admin/roles?role_id=$rid";
                        $msg = Message::getMessage(3);
                    }
                } else {
                    $re = Admin::createRole($name, $des);
                    if ($re) {
                        $url = "/admin/roles?role_id=$re";
                        $msg = Message::getMessage(0, 'create successfully', 'success');
                    } else {
                        $url = "/admin/add_role";
                        $msg = Message::getMessage(2);
                    }
                }
            }
            if ($msg) {
                getOValue()->addOldOnce('admin_msg', $msg);
            }
            \Flight::redirect($url);
        } else {
            getView()->render('admin/role_info');
        }
    }

    public static function delete_role()
    {
        if (self::checkMethod('POST')) {
            $rid = self::getInput('role_id');
            $re = Admin::deleteRole((int)$rid);
            \Flight::json(['result'=>$re]);
            return false;
        }
        \Flight::redirect('/404', 404);
        return false;
    }

    public static function departments()
    {
        $did = self::getInput('department_id');
        if ($did) {
            $dep = Admin::getDepartment($did);
            getView()->render('admin/department_info', ['department' => $dep, 'department_id'=>$did]);
        } else {
            $dep = Admin::getDepartment();
            $uhead = ['department_id', 'name', 'descr', 'created_at'];
            getAssets()->addLibrary(['bootstrap-table', 'bootstrap-dialog']);
            getView()->render('admin/departments', ['departments' => FormatUtils::formatTransArray($dep), 'departments_head' => FormatUtils::formatHead($uhead)]);
        }
    }

    public static function add_department()
    {
        if (self::checkMethod('POST')) {
            $did = self::getInput('department_id');
            $name = self::getInput('name');
            $des = self::getInput('descr');
            $new = self::getInput('new', false);
            if (!self::checkExists($did, null, false) || !self::checkExists($name, null, false)) {
                $msg = Message::getMessage(1);
                $url = '/admin/add_department';
            } else {
                if (!$new) {
                    $re = Admin::updateDepartment($did, $name, $des);
                    if ($re) {
                        $url = "/admin/departments?department_id=$did";
                        $msg = Message::getMessage(0, 'update successfully', 'success');
                    } else {
                        $url = "/admin/departments?department_id=$did";
                        $msg = Message::getMessage(3);
                    }
                } else {
                    $re = Admin::createDepartment($did, $name, $des);
                    if ($re) {
                        $url = "/admin/departments?department_id=$re";
                        $msg = Message::getMessage(0, 'create successfully', 'success');
                    } else {
                        $url = "/admin/add_department";
                        $msg = Message::getMessage(2);
                    }
                }
            }
            if ($msg) {
                getOValue()->addOldOnce('admin_msg', $msg);
            }
            \Flight::redirect($url);
        } else {
            getView()->render('admin/department_info');
        }
    }

    public static function delete_department()
    {
        if (self::checkMethod('POST')) {
            $did = self::getInput('department_id');
            $re = Admin::deleteDepartment($did);
            \Flight::json(['result'=>$re]);
            return false;
        }
        \Flight::redirect('/404', 404);
        return false;
    }

    public static function apps()
    {
        $appid = self::getInput('app_id');
        $head = ['app_name', 'app_id', 'descr', 'app_secret', 'redirect_uri', 'created_at', 'updated_at'];
        if ($appid) {
            $apps = getORM()->getModel('Apps');
            $app = $apps->show($appid, $head);
            getView()->render('admin/app_info', ['app_id'=>$appid, 'app' => FormatUtils::formatTransArray($app)]);
        } else {
            $apps = getORM()->getModel('Apps');
            $apps = $apps->lists($head);
            getAssets()->addLibrary(['bootstrap-table', 'bootstrap-dialog']);
            getView()->render('admin/apps', ['apps' => FormatUtils::formatTransArray($apps), 'apps_head' => FormatUtils::formatHead($head)]);
        }
    }

    public static function add_app()
    {
        if (self::checkMethod('POST')) {
            $appid = self::getInput('app_id');
            $app_name = self::getInput('app_name');
            $uri = self::getInput('redirect_uri');
            $app = self::getArrayInput(['app_name', 'redirect_uri', 'descr']);
            if (!self::checkExists($app_name, null, false) || !self::checkExists($uri, null, false)) {
                $msg = Message::getMessage(1, 'invalid app_name or redirect_uri');
                $url = '/admin/add_app';
            } else {
                $apps = getORM()->getModel('Apps');
                if ($appid) {
                    $re = $apps->update($app, ['app_id'=>$appid]);
                    if ($re) {
                        $url = "/admin/apps?app_id=$appid";
                        $msg = Message::getMessage(0, 'update successfully', 'success');
                    } else {
                        $url = "/admin/apps?app_id=$appid";
                        $msg = Message::getMessage(3);
                    }
                } else {
                    $re = $apps->getAppId($app_name);
                    if ($re) {
                        $url = "/admin/add_app";
                        $msg = Message::getMessage(2);
                    } else {
                        $apps->insert($app);
                        $re = $apps->getAppId($app_name);
                        if ($re) {
                            $url = "/admin/apps?app_id=$re";
                            $msg = Message::getMessage(0, 'update successfully', 'success');
                        } else {
                            $url = "/admin/add_app";
                            $msg = Message::getMessage(2);
                        }
                    }
                }
            }
            if ($msg) {
                getOValue()->addOldOnce('admin_msg', $msg);
            }
            \Flight::redirect($url);
        } else {
            getView()->render('admin/app_info');
        }
    }

    public static function delete_app()
    {
        if (self::checkMethod('POST')) {
            $aid = self::getInput('app_id');
            $re = getORM()->getModel('Apps')->delete($aid);
            \Flight::json(['result'=>$re]);
            return false;
        }
        \Flight::redirect('/404');
        return false;
    }
} 