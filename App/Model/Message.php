<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/15
 * Time: 18:17
 */

namespace App\Model;


class Message
{

    /**
     * @param int $code
     * @param string $message
     * @param string $type
     * @return array
     */
    public static function getMessage($code = 0, $message = '', $type = 'danger')
    {
        switch ($code) {
            case 1: $err = ['message'=>'illegal id or name', 'code'=>$code]; break;
            case 2: $err = ['message'=>'fail to create', 'code'=>$code]; break;
            case 3: $err = ['message'=>'fail to update', 'code'=>$code]; break;
            case 4: $err = ['message'=>'fail to delete', 'code'=>$code]; break;
            case 100000: $err = ['message'=>'Invalid response_type', 'code'=>$code]; break;
            case 100001: $err = ['message'=>'Invalid client_id', 'code'=>$code]; break;
            case 100004: $err = ['message'=>'Invalid grant_type', 'code'=>$code]; break;
            case 100005: $err = ['message'=>'Invalid code', 'code'=>$code]; break;
            case 100007: $err = ['message'=>'Invalid access_token', 'code'=>$code]; break;
            case 100008: $err = ['message'=>'Client_id not exists', 'code'=>$code]; break;
            case 100009: $err = ['message'=>'Illegal client secret', 'code'=>$code]; break;
            case 100010: $err = ['message'=>'Illegal redirect_uri', 'code'=>$code]; break;
            case 100016: $err = ['message'=>'Unable to verify access_token', 'code'=>$code]; break;
            case 100018: $err = ['message'=>'Unable to retrieve code', 'code'=>$code]; break;
            case 100019: $err = ['message'=>'Unable to retrieve access_token by code', 'code'=>$code]; break;
            case 100023: $err = ['message'=>'The right is not granted to this access_token', 'code'=>$code]; break;
            default: $err = ['message'=>'', 'code'=>0]; break;
        }
        if ($message) {
            $err['message'] = $message;
        }
        if ($type) {
            $err['type'] = $type;
        }
        return $err;
    }
}