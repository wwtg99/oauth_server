<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/16
 * Time: 10:42
 */

namespace App\Controller;


use App\Model\AccessToken;
use App\Model\ErrorMsg;
use Flight2wwu\Common\BaseController;

class TokenController extends BaseController
{

    /**
     * Check access_token and scope.
     *
     * @param string $scope
     * @return array|string: array for error and string for user_id if ok
     */
    protected static function check($scope = '')
    {
        $re = self::tokenError();
        if ($re !== false) {
            return $re;
        }
        $token = self::tokenInfo();
        if ($scope) {
            $re = self::scopeError($token['scope'], $scope);
            if ($re !== false) {
                return $re;
            }
        }
        return $token['user_id'];
    }

    /**
     * Check access_token and app_key.
     *
     * @return array|bool
     * @throws \Exception
     */
    protected static function tokenError()
    {
        $token = self::getInput('access_token');
        $akey = self::getInput('app_key');
        if (!self::checkExists($token, null, false)) {
            $redata = ['error'=>ErrorMsg::getError(100007)];
        } else {
            if(AccessToken::verify($token, $akey)) {
                return false;
            } else {
                $redata = ['error'=>ErrorMsg::getError(100016)];
            }
        }
        return $redata;
    }

    /**
     * Get authorized info [user_id, scope].
     *
     * @return array
     */
    protected static function tokenInfo()
    {
        $token = self::getInput('access_token');
        $akey = self::getInput('app_key');
        return AccessToken::getToken($token, $akey);
    }

    /**
     * Check scope if is authorized.
     *
     * @param array|string $scopes
     * @param string $need_scope
     * @return bool|array
     */
    protected static function scopeError($scopes, $need_scope)
    {
        if (!is_array($scopes)) {
            $scopes = explode(',', $scopes);
        }
        if (in_array($need_scope, $scopes)) {
            return false;
        } else {
            return ['error'=>ErrorMsg::getError(100023)];
        }
    }
}