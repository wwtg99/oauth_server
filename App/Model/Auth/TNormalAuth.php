<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/14
 * Time: 11:36
 */

namespace App\Model\Auth;


trait TNormalAuth
{

    /**
     * @param array $user
     * @return array|bool
     * @throws \Exception
     */
    public static function verify(array $user)
    {
        $db = getDB();
        if (array_key_exists('token', $user) && $user['token']) {
            //unused
            $u = $db->getConnection('main')->get(User::VIEW_USER, User::$head, ['AND'=>['remember_token'=>$user['token'], 'active'=>'true']]);
            if ($u) {
                if (self::verifyToken($user['token'], $u[User::KEY_USER_ID])) {
                    return ['user_id'=>$u[User::KEY_USER_ID], 'name'=>$u[User::KEY_USER_NAME], 'label'=>$u['label'], 'department'=>$u['department'], 'email'=>$u[User::KEY_USER_EMAIL], 'superuser'=>$u['superuser'], 'descr'=>$u['descr'], 'token'=>$user[User::KEY_USER_TOKEN], 'roles'=>$u['roles']];
                }
            }
        } elseif (array_key_exists('username', $user) && $user['username']) {
            $u = $db->getConnection('main')->get(User::VIEW_USER, User::$head, ['AND'=>[User::KEY_USER_NAME => $user['username'], 'active' => 'true']]);
            if ($u) {
                $uid = $u[User::KEY_USER_ID];
                $pwd = $u[User::KEY_USER_PASSWORD];
//                $age = $user['remember'] ? 30 : 1;
                if (is_null($pwd) || $pwd === '' || password_verify($user['password'], $pwd)) {
//                    $token = self::generateToken($uid, $age);
//                    self::updateToken($uid, $token);
                    return ['user_id' => $uid, 'name' => $u[User::KEY_USER_NAME], 'label'=>$u['label'], 'department'=>$u['department'], 'email' => $u[User::KEY_USER_EMAIL], 'superuser' => $u['superuser'], 'descr' => $u['descr'], 'roles'=>$u['roles']];
                }
            }
        }
        return false;
    }

    /**
     * @param string $uid
     * @param int $age
     * @return string
     */
    private static function generateToken($uid, $age = 1)
    {
        $ip = \Flight::request()->ip;
        if (is_int($age)) {
            $time = strtotime("+$age day");
        } else {
            $time = strtotime("+1 day");
        }
        return base64_encode(implode(';', [$uid, $ip, $time]));
    }

    /**
     * @param string $token
     * @param string $uid
     * @return bool
     */
    private static function verifyToken($token, $uid)
    {
        if (!$token) {
            return false;
        }
        $code = base64_decode($token);
        $c = explode(';', $code);
        if ($uid != $c[0]) {
            return false;
        }
        if ($c[1] != \Flight::request()->ip) {
            return false;
        }
        if ($c[2] < time()) {
            return false;
        }
        return true;
    }

    /**
     * @param string $uid
     * @param string $token
     * @return bool|int
     */
    private static function updateToken($uid, $token)
    {
        $db = getDB();
        $re= $db->getConnection('main')->update(User::TABLE_USER, ['remember_token'=>$token], [User::KEY_USER_ID=>$uid]);
        return $re;
    }
}