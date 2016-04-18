<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/10/8
 * Time: 10:47
 */

namespace App\Model\Auth;

/**
 * Class User
 * @package wwu\App\Model
 */
class User
{

    use TNormalAuth;
//    use TOpenAuth;

    const KEY_USER_ID = 'user_id';
    const KEY_USER_NAME = 'name';
    const KEY_USER_PASSWORD = 'password';
    const KEY_USER_EMAIL = 'email';
    const KEY_USER_TOKEN = 'remember_token';
    const TABLE_USER = 'users';
    const VIEW_USER = 'view_users';

    public static $head = '*';

    /**
     * @param $old
     * @param $new
     * @return bool
     * @throws \Exception
     */
    public static function changePassword($old, $new)
    {
        $uid = getUser('user_id');
        if ($uid) {
            $db = getDB();
            $re = $db->getConnection('main')->get(User::TABLE_USER, ['password'], ['AND'=>['user_id'=>$uid]]);
            $pwd = $re['password'];
            if (is_null($pwd) || $pwd === '' || password_verify($old, $pwd)) {
                $pwd = password_hash($new, PASSWORD_BCRYPT);
                $re = $db->getConnection('main')->update(User::TABLE_USER, ['password'=>$pwd], ['AND'=>[User::KEY_USER_ID=>$uid]]);
                if ($re) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param array $user
     * @return bool
     */
    public static function updateInfo($user)
    {
        $uid = $user['user_id'];
        $db = getDB();
        $sets = [];
        foreach ($user as $k => $v) {
            if ($k == 'user_id' || $k == 'password') {
                continue;
            }
            array_push($sets, "$k = $v");
        }
        $s = implode(',', $sets);
        $re = $db->exec("update users set $s where id = :uid", ['uid'=>$uid]);
        if ($re) {
            return true;
        }
        return false;
    }
} 