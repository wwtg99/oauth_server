<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/16
 * Time: 9:57
 */

namespace App\Controller;


use App\Model\Admin;

class UserController extends TokenController
{

    public static function info()
    {
        $err = self::tokenError();
        if ($err) {
            \Flight::json($err);
            return false;
        } else {
            $token = self::tokenInfo();
            if ($token) {
                $err = self::scopeError($token['scope'], 'get_user_info');
                if ($err) {
                    \Flight::json($err);
                    return false;
                } else {
                    $user = Admin::getUser($token['user_id']);
                    if ($user) {
                        \Flight::json($user);
                        return false;
                    } else {
                        \Flight::json(['error'=>['message'=>'Invalid user', 'code'=>200000]]);
                    }
                }
            }
        }
    }
}