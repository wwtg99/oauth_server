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
        $re = self::check('get_user_info');
        if (is_array($re)) {
            \Flight::json($re);
            return false;
        } else {
            $user = Admin::getUser($re);
            if ($user) {
                \Flight::json($user);
                return false;
            } else {
                \Flight::json(['error'=>['message'=>'Invalid user', 'code'=>2]]);
                return false;
            }
        }
    }
}