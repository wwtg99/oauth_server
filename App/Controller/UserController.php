<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/16
 * Time: 9:57
 */

namespace App\Controller;


use App\Model\Admin;
use App\Model\Message;

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
                $msg = Message::getMessage(1);
                \Flight::json(['error'=>$msg]);
                return false;
            }
        }
    }
}