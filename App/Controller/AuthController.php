<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 15:53
 */

namespace App\Controller;

use App\Model\Auth\User;
use Flight2wwu\Common\BaseController;

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

    private static function getlogin()
    {
        if (getAuth()->isLogin()) {
            \Flight::redirect(self::$logoutPath);
        } else {
            getView()->render('auth/login');
        }
    }

    private static function postLogin()
    {
        $name = self::getPost('username');
        $pwd = self::getPost('password');
        $rem = self::getPost('remember');
        getOValue()->addOld('username', $name);
        if (getAuth()->attempt(['username'=>$name, 'password'=>$pwd, 'remember'=>$rem])) {
            \Flight::redirect(self::$redirectPath);
        } else {
            getView()->render('auth/login', ['msg'=>'login failed', 'status'=>'danger']);
        }
    }

    private static function getLogout()
    {
        getView()->render('auth/logout', getUser());
    }

    private static function postLogout() {
        getAuth()->logout();
        \Flight::redirect(self::$redirectPath);
    }

    private static function getChangePwd()
    {
        if (getAuth()->isLogin()) {
            getView()->render('auth/change_pwd');
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
            $msg = 'input password';
            $status = 'danger';
        } elseif ($new1 != $new2) {
            $msg = 'password mismatch';
            $status = 'danger';
        } else {
            if (User::changePassword($old, $new1)) {
                $msg = 'password changed';
                $status = 'success';
            } else {
                $msg = 'password not changed';
                $status = 'danger';
            }
        }
        getView()->render('auth/change_pwd', ['msg'=>$msg, 'status'=>$status]);
        return false;
    }

    private static function getInfo()
    {

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
} 