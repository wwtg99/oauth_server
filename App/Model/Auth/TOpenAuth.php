<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/29
 * Time: 13:31
 */

namespace App\Model\Auth;


use Flight2wwu\Component\Utils\FormatUtils;
use GuzzleHttp\Client;
use Purl\Url;

trait TOpenAuth
{

    /**
     * @var string
     */
    private static $userInfoPath = 'http://192.168.0.21:10000/user/info';

    private static $syncUser = true;

    /**
     * @param array $user
     * @return array|bool
     */
    public static function verify(array $user)
    {
        if (isset($user['token'])) {
            $token = $user['token'];
            $u = self::getUser($token);
            if ($u && !array_key_exists('error', $u)) {
                if (self::$syncUser) {
                    User::syncUser($u);
                }
                $u[User::KEY_USER_TOKEN] = $token;
                if (array_key_exists('expires_in', $user)) {
                    $u['expires_in'] = $user['expires_in'] + time();
                }
                return $u;
            }
        }
        return false;
    }

    /**
     * @param bool $state
     * @param array $params
     * @return string
     */
    public static function getAuthorizeUri($state = false, $params = [])
    {
        $oauth = \Flight::get('oauth');
        $appid = isset($oauth['app_id']) ? $oauth['app_id'] : '';
        $appid_key = isset($oauth['app_id_key']) ? $oauth['app_id_key'] : '';
        $redirect_uri = isset($oauth['redirect_uri']) ? $oauth['redirect_uri'] : '';
        $redirect_uri_key = isset($oauth['redirect_uri_key']) ? $oauth['redirect_uri_key'] : '';
        $state_key = isset($oauth['state_key']) ? $oauth['state_key'] : '';
        $server_uri = isset($oauth['code_uri']) ? $oauth['code_uri'] : '';
        if ($server_uri && $redirect_uri && $redirect_uri_key) {
            $params[$redirect_uri_key] = $redirect_uri;
            $params['response_type'] = 'code';
            //check state
            getCache()->set('oauth_check_state', $state);
            if ($state_key && $state) {
                $s = FormatUtils::randStr(10);
                getCache()->set('oauth_state', $s, 3600 * 24);
                $params[$state_key] =  $s;
            }
            if ($appid && $appid_key) {
                $params[$appid_key] = $appid;
            }
            $uri = new Url($server_uri);
            $uri->query->setData($params);
            return $uri->getUrl();
        }
        return '';
    }

    /**
     * @param string $code
     * @param string $state
     * @param array $params
     * @return string|null
     */
    public static function getAccessToken($code, $state = null, $params = [])
    {
        $oauth = \Flight::get('oauth');
        $server_uri = $oauth['token_uri'];
        $appid = isset($oauth['app_id']) ? $oauth['app_id'] : '';
        $appid_key = isset($oauth['app_id_key']) ? $oauth['app_id_key'] : '';
        $appsec = isset($oauth['app_secret']) ? $oauth['app_secret'] : '';
        $appsec_key = isset($oauth['app_secret_key']) ? $oauth['app_secret_key'] : '';
        $redirect_uri = isset($oauth['redirect_uri']) ? $oauth['redirect_uri'] : '';
        $redirect_uri_key = isset($oauth['redirect_uri_key']) ? $oauth['redirect_uri_key'] : '';
        $state_key = isset($oauth['state_key']) ? $oauth['state_key'] : '';
        if ($server_uri && $redirect_uri && $redirect_uri_key && $code) {
            $params[$redirect_uri_key] = $redirect_uri;
            $params['grant_type'] = 'authorization_code';
            $params['code'] = $code;
            //check state
            $check_state = boolval(getCache()->get('oauth_check_state'));
            if ($check_state) {
                $send_state = getCache()->get('oauth_state');
                if ($send_state != $state) {
                    return null;
                }
                $send_state = FormatUtils::randStr(10);
                $params[$state_key] =  $send_state;
            }
            //app id and secret
            if ($appid && $appid_key && $appsec && $appsec_key) {
                $params[$appid_key] = $appid;
                $params[$appsec_key] = $appsec;
            }
            $uri = new Url($server_uri);
            $uri->query->setData($params);
            $client = new Client();
            $res = $client->get($uri->getUrl());
            if ($res) {
                $json = json_decode($res->getBody(), true);
                //check state
                if ($check_state) {
                    $get_state = $json[$state_key];
                    if (isset($send_state) && $get_state != $send_state) {
                        return null;
                    }
                }
                return $json;
            }
        }
        return null;
    }

    /**
     * @param array $user
     * @return array
     * @throws \Exception
     */
    public static function refreshUser($user)
    {
        if (isset($user[User::KEY_USER_TOKEN])) {
            $user = self::getUser($user[User::KEY_USER_TOKEN]);
            if ($user) {
                return $user;
            }
        }
        return [];
    }

    /**
     * @param $user
     * @return bool
     * @throws \Exception
     */
    public static function syncUser($user)
    {
        if ($user && isset($user['user_id']) && isset($user['name'])) {
            $db = getDB()->getConnection();
            $u = $db->get('users', '*', [User::KEY_USER_ID=>$user['user_id']]);
            if (!$u) {
                $db->insert('users', [User::KEY_USER_ID=>$user['user_id'], User::KEY_USER_NAME=>$user['name'], User::KEY_USER_EMAIL=>$user['email'], 'department_id'=>$user['department_id'], 'label'=>$user['label']]);
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $token
     * @return array
     */
    private static function getUser($token)
    {
        $res = self::getResource(self::$userInfoPath, $token);
        return $res;
    }

    /**
     * @param $api_uri
     * @param $token
     * @param array $params
     * @return array
     */
    private static function getResource($api_uri, $token, $params = [])
    {
        $oauth = \Flight::get('oauth');
        $appid = isset($oauth['app_id']) ? $oauth['app_id'] : '';
        if ($appid) {
            $params['app_key'] = $appid;
        }
        $params['access_token'] = $token;
        $uri = new Url($api_uri);
        $uri->query->setData($params);
        $client = new Client();
        $res = $client->get($uri->getUrl());
        return json_decode($res->getBody(), true);
    }
}