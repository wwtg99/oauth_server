<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/18
 * Time: 10:40
 */

namespace App\Model\Orm;


use Flight2wwu\Component\Database\OrmModel;

class Apps extends OrmModel
{

    const CODE_LIFETIME = 600;//10 minutes

    protected $tableName = 'apps';
    protected $tableKey = 'app_id';

    /**
     * @param $app_name
     * @return string
     * @throws \Exception
     */
    public function getAppId($app_name)
    {
        $db = getDB();
        $re = $db->getConnection()->get($this->getTableName(), 'app_id', ['app_name'=>$app_name]);
        if ($re) {
            return $re;
        }
        return '';
    }

    /**
     * @param string $user_id
     * @param string $client_id
     * @param string $redirect_uri
     * @param string $scope
     * @param string $state
     * @return string
     */
    public function generateCode($user_id, $client_id, $redirect_uri, $scope, $state = null)
    {
        $app = $this->show($client_id);
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
        $expires = time() + self::CODE_LIFETIME;
        getCache()->set($code, ['expires'=>$expires, 'scope'=>$scope, 'user_id'=>$user_id], self::CODE_LIFETIME);
        return $url->getUrl();
    }

    /**
     * @param string $user_id
     * @param string $scope
     * @param string $client_id
     * @return array
     */
    public function generateAccessToken($user_id, $scope, $client_id = null)
    {
        if ($client_id) {
            $expires = 86400 * 30;
        } else {
            $expires = 86400;
        }
        $exp = date('Y-m-d', time() + $expires);
        $db = getDB();
        $re = $db->queryOne("insert into access_tokens (user_id, app_id, expires_in, scope) values (:uid, :aid, :ex, :sp) returning id", ['uid'=>$user_id, 'aid'=>$client_id, 'ex'=>$exp, 'sp'=>$scope]);
        if ($re && $re['id']) {
            $token = $db->getConnection()->get('access_tokens', 'access_token', ['id'=>$re['id']]);
            return ['access_token'=>$token, 'expires_in'=>$expires];
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
}