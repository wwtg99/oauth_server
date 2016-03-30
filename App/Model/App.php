<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/15
 * Time: 14:20
 */

namespace App\Model;


class App
{

    /**
     * @param $app_id
     * @return array
     * @throws \Exception
     */
    public static function show($app_id)
    {
        if (!$app_id) {
            return [];
        }
        $db = getDB();
        $head = ['app_id', 'app_name', 'descr', 'app_secret', 'redirect_uri', 'created_at', 'updated_at'];
        $re = $db->getConnection()->get('apps', $head, ['app_id'=>$app_id]);
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param $redirect_uris
     * @param $url
     * @return bool
     */
    public static function checkRedirectUrl($redirect_uris, $url)
    {
        $urlarr = explode(';', $redirect_uris);
        $uarr = parse_url($url);
        $path = $uarr['host'];
        foreach ($urlarr as $item) {
            if ($item == $path) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $user_id
     * @param string $client_id
     * @param string $redirect_uri
     * @param string $scope
     * @param string $state
     * @return string
     */
    public static function generateCode($user_id, $client_id, $redirect_uri, $scope, $state = null)
    {
        $app = App::show($client_id);
        if ($app) {
            //registered app
            $code = strtoupper(uniqid('R'));
        } else {
            //unregistered app
            $code = strtoupper(uniqid('U'));
        }
        $url = new \Purl\Url($redirect_uri);
        $url->query->set('code', $code);
        if ($state) {
            $url->query->set('state', $state);
        }
        $expires = time() + 600;//10 minutes
        getCache()->set($code, ['expires'=>$expires, 'scope'=>$scope, 'user_id'=>$user_id], 600);
        return $url->getUrl();
    }

    /**
     * @param string $user_id
     * @param string $scope
     * @param string $client_id
     * @return array
     */
    public static function generateAccessToken($user_id, $scope, $client_id = null)
    {
        if ($client_id) {
            $expires = 86400 * 30;
        } else {
            $expires = 86400;
        }
        $db = getDB();
        $re = $db->queryOne("select create_token(:uid, :aid, :ex, :sp)", ['uid'=>$user_id, 'aid'=>$client_id, 'ex'=>$expires, 'sp'=>$scope]);
        if ($re && $re['create_token']) {
            return ['access_token'=>$re['create_token'], 'expires_in'=>$expires];
        }
        return [];
    }
}