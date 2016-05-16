<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 15:53
 */

namespace App\Controller;

use App\Model\Auth\User;
use App\Model\Message;
use Flight2wwu\Common\BaseController;
use Flight2wwu\Component\Utils\FormatUtils;

class AuthController extends BaseController
{

    /**
     * Path to login.
     *
     * @var string
     */
    public static $loginPath = '/auth/login';

    /**
     * Path to logout.
     *
     * @var string
     */
    public static $logoutPath = '/auth/logout';

    /**
     * Path to redirect after login.
     *
     * @var string
     */
    public static $redirectPath = '/';

    public static function login()
    {
        if (self::checkMethod('POST')) {
            AuthController::postLogin();
        } else {
            AuthController::getLogin();
        }
    }

    public static function logout()
    {
        if (self::checkMethod('POST')) {
            AuthController::postLogout();
        } else {
            AuthController::getLogout();
        }
    }

    public static function password()
    {
        if (self::checkMethod('POST')) {
            AuthController::postChangePwd();
        } else {
            AuthController::getChangePwd();
        }
    }

    public static function info()
    {
        if (self::checkMethod('POST')) {
            AuthController::postInfo();
        } else {
            AuthController::getInfo();
        }
    }

    public static function signup()
    {
        if (self::checkMethod('POST')) {
            AuthController::postSignup();
        } else {
            AuthController::getSignup();
        }
    }

    public static function user_edit()
    {
        if (self::checkMethod('POST')) {
            AuthController::postEdit();
        } else {
            AuthController::getEdit();
        }
    }

    private static function getlogin()
    {
        if (getAuth()->isLogin()) {
            \Flight::redirect(self::$logoutPath);
        } else {
            getView()->render('auth/login', ['title'=>'Login']);
        }
    }

    private static function postLogin()
    {
        $name = self::getPost('username');
        $pwd = self::getPost('password');
        $rem = self::getPost('remember');
        getOValue()->addOld('username', $name);
        if (getAuth()->attempt(['username'=>$name, 'password'=>$pwd, 'remember'=>$rem], false)) {
            $path = getOValue()->getOldOnce('last_path');
            if ($path) {
                self::$redirectPath = $path;
            }
            \Flight::redirect(self::$redirectPath);
        } else {
            $msg = Message::getMessage(5);
            getView()->render('auth/login', ['msg'=>$msg, 'title'=>'Login']);
        }
    }

    private static function getLogout()
    {
        getView()->render('auth/logout', ['user'=>getUser(), 'title'=>'Logout']);
    }

    private static function postLogout() {
        getAuth()->logout();
        \Flight::redirect(self::$redirectPath);
    }

    private static function getChangePwd()
    {
        if (getAuth()->isLogin()) {
            getView()->render('auth/change_pwd', ['title'=>'Change Password']);
        } else {
            \Flight::redirect(self::$loginPath);
        }
    }

    private static function postChangePwd()
    {
        $old = self::getPost('old');
        $new1 = self::getPost('new1');
        $new2 = self::getPost('new2');
        if (!self::checkExists($new1, null, false) || !self::checkExists($new2, null, false)){
            $msg = Message::getMessage(9);
        } elseif ($new1 != $new2) {
            $msg = Message::getMessage(6);
        } else {
            if (User::changePassword($old, $new1)) {
                $msg = Message::getMessage(7);
            } else {
                $msg = Message::getMessage(8);
            }
        }
        getView()->render('auth/change_pwd', ['msg'=>$msg, 'title'=>'Change Password']);
        return false;
    }

    private static function getInfo()
    {
        $user = getUser();
        getView()->render('auth/user_info', ['user'=>FormatUtils::formatTransArray($user), 'title'=>'User Info']);
    }

    private static function postInfo()
    {

    }

    private static function getSignup()
    {

    }

    public static function postSignup()
    {

    }

    private static function getEdit()
    {
        $user = getUser();
        getView()->render('auth/user_edit', ['user'=>$user, 'title'=>'Edit User']);
    }

    private static function postEdit()
    {
        $user = self::getArrayInput(['name', 'label', 'email', 'descr']);
        $u = getORM()->getModel('Users');
        $re = $u->update($user, ['user_id'=>getUser('user_id')]);
        if ($re) {
            $msg = Message::getMessage(0, 'update successfully', 'success');
        } else {
            $msg = Message::getMessage(3);
        }
        getOValue()->addOldOnce('user_msg', $msg);
        $user = getAuth()->refreshUser(getUser());
        getView()->render('auth/user_edit', ['user'=>$user, 'title'=>'Edit User', 'msg'=>$msg]);
    }
} 