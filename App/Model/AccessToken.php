<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/16
 * Time: 10:49
 */

namespace App\Model;


class AccessToken
{

    /**
     * @param $token
     * @param $app_id
     * @return bool
     */
    public static function verify($token, $app_id)
    {
        $db = getDB()->getConnection();
        $re = $db->queryOne("select verify_token(:tk, :app)", ['tk'=>$token, 'app'=>$app_id]);
        if ($re) {
            return boolval($re['verify_token']);
        }
        return false;
    }

    /**
     * @param $token
     * @param $app_id
     * @return array
     * @throws \Exception
     */
    public static function getToken($token, $app_id)
    {
        $db = getDB()->getConnection();
        $re = $db->get('access_tokens', ['user_id', 'scope'], ['AND'=>['access_token'=>$token, 'app_id'=>$app_id, '#expires_in[>=]'=>'NOW()']]);
        if ($re) {
            return $re;
        }
        return [];
    }
}