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
        $db = getDB()->getConnection('main');
        if (isset($user['username']) && isset($user['password'])) {
            $u = $db->get(User::VIEW_USER, User::$head,
                ['AND'=>[User::KEY_USER_NAME => $user['username'], 'deleted_at' => null]]);
            if ($u) {
                $uid = $u[User::KEY_USER_ID];
                $pwd = $u[User::KEY_USER_PASSWORD];
                $age = $user['remember'] ? 30 : 1;
                $u['expires_in'] = $age * 86400;
                if (is_null($pwd) || $pwd === '' || password_verify($user['password'], $pwd)) {
                    $token = self::generateToken($uid, $age);
                    $t = self::updateToken($uid, $token);
                    if ($t) {
                        $u[User::KEY_USER_TOKEN] = $token;
                    }
                    return $u;
                }
            }
        } elseif (isset($user['username']) && isset($user['token'])) {
            $u = $db->get(User::VIEW_USER, User::$head,
                ['AND'=>[User::KEY_USER_TOKEN=>$user['token'], User::KEY_USER_NAME=>$user['username'], 'deleted_at'=>null]]);
            if ($u) {
                if (self::verifyToken($user['token'], $u[User::KEY_USER_ID])) {
                    return $u;
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
        $db = getDB()->getConnection('main');
        $re= $db->update(User::TABLE_USER, ['remember_token'=>$token], [User::KEY_USER_ID=>$uid]);
        return $re;
    }
}