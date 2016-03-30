<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/29
 * Time: 16:14
 */

namespace App\Controller;


use App\Model\Auth\User;
use Flight2wwu\Common\BaseController;
use League\Flysystem\Exception;

class OAuthController extends BaseController
{

    /**
     * Path to redirect after login.
     *
     * @var string
     */
    public static $redirectPath = '/';

    public static function login()
    {
        $scope = ['get_user_info']; //request scope
        $params = ['scope'=>implode(',', $scope)];
        $check_state = false;
        $uri = User::getAuthorizeUri($check_state, $params);
        if ($uri) {
            \Flight::redirect($uri);
        } else {
            throw new Exception('illegal oauth');
        }
        return false;
    }

    public static function redirect_login()
    {
        $code = self::getInput('code');
        $state = self::getInput('state');
        if ($code) {
            $token = User::getAccessToken($code, $state);
            if (isset($token['access_token']) && isset($token['expires_in'])) {
                if (getAuth()->attempt(['token'=>$token['access_token'], 'expires_in'=>$token['expires_in']], false)) {
                    \Flight::redirect(self::$redirectPath);
                    return false;
                }
            }
        }
        throw new Exception('login error');
    }
}