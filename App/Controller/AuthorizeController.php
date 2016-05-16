<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/15
 * Time: 14:14
 */

namespace App\Controller;


use App\Model\Auth\User;
use App\Model\Message;
use App\Model\Orm\Apps;
use Flight2wwu\Common\BaseController;
use Flight2wwu\Common\FWException;

class AuthorizeController extends BaseController
{

    /**
     * If allow unregistered app (no client_id) to login or not
     */
    const ALLOW_UNREGISTERED = true;

    /**
     * @var array
     */
    private static $scopes = ['get_user_info', 'update_user_info', 'send_email'];

    public static function authorize()
    {
        if (self::checkMethod('POST')) {
            $username = self::getInput('username');
            $pwd = self::getInput('password');
            $scope = self::getInput('scope');
            $cid = self::getInput('client_id');
            $state = self::getInput('state');
            $redirect_uri = self::getInput('redirect_uri');
            if (!self::checkExists($username, null, false) || !self::checkExists($pwd, null, false)) {
                $redata['msg'] = 'login failed';
                $redata['status'] = 'danger';
            } elseif (!self::checkExists($scope, null, false)) {
                $redata['msg'] = 'invalid scope';
                $redata['status'] = 'danger';
            } else {
                $redata = self::grantCode($username, $pwd, $scope, $redirect_uri, $cid, $state);
            }
            if (is_array($redata)) {
                getView()->render(['center'=>'authorize/grant', 'head'=>'authorize/grant_head', 'foot'=>'', 'left'=>'', 'right'=>''], $redata);
            } else {
                \Flight::redirect($redata);
            }
            return false;
        } else {
            $rtype = self::getInput('response_type', 'code');
            $cid = self::getInput('client_id');
            $rurl = self::getInput('redirect_uri');
            $state = self::getInput('state');
            $scope = self::getInput('scope', 'get_user_info');
            if (!self::checkExists($rurl, null, false)) {
                $redata = ['error' => Message::getMessage(100010)];
            } else {
                if ($rtype == 'code') {
                    $redata = self::responseCode($cid, $rurl, $scope, $state);
                } else {
                    $err = Message::getMessage(100000);
                    $redata = ['error' => $err];
                }
            }
            getView()->render(['center'=>'authorize/grant', 'head'=>'authorize/grant_head', 'foot'=>'', 'left'=>'', 'right'=>''], $redata);
            return false;
        }
    }

    public static function token()
    {
        $gtype = self::getInput('grant_type', 'authorization_code');
        $cid = self::getInput('client_id');
        $cset = self::getInput('client_secret');
        $code = self::getInput('code');
        $rurl = self::getInput('redirect_uri');
        $state = self::getInput('state');
        if (!self::checkExists($rurl, null, false)) {
            $redata = ['error' => Message::getMessage(100010)];
        } elseif(!self::checkExists($code, null, false)) {
            $redata = ['error' => Message::getMessage(100005)];
//        } elseif(!self::checkExists($cid, null, false)) {
//            $redata = ['error' => Message::getMessage(100001)];
//        } elseif(!self::checkExists($cset, null, false)) {
//            $redata = ['error' => Message::getMessage(100009)];
        } else {
            if ($gtype == 'authorization_code') {
                $redata = self::authorizeToken($cid, $cset, $code, $rurl, $state);
            } else {
                $redata = ['error' => $err = Message::getMessage(100004)];
            }
        }
        \Flight::json($redata);
        return false;
    }

    /**
     * @param $scope
     * @return array
     */
    private static function scopes($scope)
    {
        if (!is_array($scope)) {
            $scope = explode(',', $scope);
        }
        $re = array_intersect($scope, self::$scopes);
        return $re;
    }

    /**
     * @param $client_id
     * @param $redirect_uri
     * @param $scope
     * @param $state
     * @return array
     */
    private static function responseCode($client_id, $redirect_uri, $scope, $state = null)
    {
        $redata = [];
        if ($state) {
            $redata['state'] = $state;
        }
        $redirect_uri = urldecode($redirect_uri);
        $apps = getORM()->getModel('Apps');
        if ($client_id) {
            //registered app
            $app = $apps->show($client_id);
            if ($app) {
                $redata['app_id'] = $client_id;
                $redata['app_name'] = $app['app_name'];
                $redata['app_descr'] = $app['descr'];
                if (Apps::checkRedirectUrl($app['redirect_uri'], $redirect_uri)) {
                    $scope = self::scopes($scope);
                    $redata['scope'] = $scope;
                    $redata['redirect_uri'] = $redirect_uri;
                } else {
                    $redata = ['error'=>Message::getMessage(100010)];
                }
            } else {
                $redata = ['error'=>Message::getMessage(100001)];
            }
        } else {
            //unregistered app
            if (self::ALLOW_UNREGISTERED) {
                $scope = self::scopes($scope);
                $redata['scope'] = $scope;
                $redata['redirect_uri'] = $redirect_uri;
            } else {
                $redata = ['error'=>Message::getMessage(100008)];
            }
        }
        return $redata;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $scope
     * @param string $redirect_uri
     * @param string $client_id
     * @param string $state
     * @return array|string
     */
    private static function grantCode($username, $password, $scope, $redirect_uri, $client_id = null, $state = null)
    {
        $redata = ['scope'=>explode(',', $scope), 'redirect_uri'=>$redirect_uri];
        if ($state) {
            $redata['state'] = $state;
        }
        $apps = getORM()->getModel('Apps');
        if ($client_id) {
            $app = $apps->show($client_id);
            $redata['app_id'] = $client_id;
            $redata['app_name'] = $app['app_name'];
            $redata['app_descr'] = $app['descr'];
            if ($app) {
                //registered app
                if (Apps::checkRedirectUrl($app['redirect_uri'], $redirect_uri)) {
                    $u = User::verify(['username' => $username, 'password' => $password, 'remember'=>false]);
                    if ($u) {
                        $uid = $u['user_id'];
                        $url = $apps->generateCode($uid, $client_id, $redirect_uri, $scope, $state);
                        return $url;
                    } else {
                        $redata['msg'] = 'login failed';
                        $redata['status'] = 'danger';
                    }
                } else {
                    $redata = ['error'=>Message::getMessage(100010)];
                }
            } else {
                $redata = ['error'=>Message::getMessage(100018)];
            }
        } else {
            //unregistered app
            if (self::ALLOW_UNREGISTERED) {
                $u = User::verify(['username'=>$username, 'password'=>$password]);
                if ($u) {
                    $uid = $u['user_id'];
                    $url = $apps->generateCode($uid, null, $redirect_uri, $scope, $state);
                    return $url;
                } else {
                    $redata['msg'] = 'login failed';
                    $redata['status'] = 'danger';
                }
            } else {
                $redata = ['error'=>Message::getMessage(100008)];
            }
        }
        return $redata;
    }

    /**
     * @param string $client_id
     * @param string $client_secret
     * @param string $code
     * @param string $redirect_uri
     * @param string $state
     * @return array
     * @throws FWException
     */
    private static function authorizeToken($client_id, $client_secret, $code, $redirect_uri, $state = null)
    {
        $redata = [];
        if ($state) {
            $redata['state'] =  $state;
        }
        $c = getCache()->get($code);
        $apps = getORM()->getModel('Apps');
        getCache()->delete($code);
        if ($c && $c['expires'] >= time()) {
            $scope = $c['scope'];
            $uid = $c['user_id'];
            if (substr($code, 0, 1) == 'R') {
                //registered app
                if ($client_id) {
                    $app = $apps->show($client_id);
                    if (!$app) {
                        throw new FWException(Message::messageList(100001));
                    }
                    if (!Apps::checkRedirectUrl($app['redirect_uri'], $redirect_uri)) {
                        $redata = ['error' => Message::getMessage(100010)];
                        return $redata;
                    } elseif ($client_secret != $app['app_secret']) {
                        $redata = ['error' => Message::getMessage(100009)];
                        return $redata;
                    } else {
                        $re = $apps->generateAccessToken($uid, $scope, $client_id);
                    }
                } else {
                    $redata = ['error' => Message::getMessage(100001)];
                    return $redata;
                }
            } else {
                //unregistered app
                $re = $apps->generateAccessToken($uid, $scope);
            }
            if ($re) {
                $redata['access_token'] = $re['access_token'];
                $redata['expires_in'] = $re['expires_in'];
            } else {
                $redata = ['error'=>Message::getMessage(100019)];
            }
        } else {
            $redata = ['error'=>Message::getMessage(100005)];
        }
        return $redata;
    }
}