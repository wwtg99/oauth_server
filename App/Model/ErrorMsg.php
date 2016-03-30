<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/15
 * Time: 14:30
 */

namespace App\Model;


class ErrorMsg
{

    /**
     * @param int $code
     * @return array
     */
    public static function getError($code)
    {
        switch($code) {
            case 100000: $msg = 'Invalid response_type'; break;
            case 100001: $msg = 'Invalid client_id'; break;
            case 100004: $msg = 'Invalid grant_type'; break;
            case 100005: $msg = 'Invalid code'; break;
            case 100007: $msg = 'Invalid access_token'; break;
            case 100008: $msg = 'Client_id not exists'; break;
            case 100009: $msg = 'Illegal client secret'; break;
            case 100010: $msg = 'Illegal redirect_uri'; break;
            case 100016: $msg = 'Unable to verify access_token'; break;
            case 100018: $msg = 'Unable to retrieve code'; break;
            case 100019: $msg = 'Unable to retrieve access_token by code'; break;
            case 100023: $msg = 'The right is not granted to this access_token'; break;
            case 200001: $msg = 'Fail to update user'; break;
            case 200002: $msg = 'Fail to create user'; break;
            default: $msg = ''; break;
        }
        return ['message'=>$msg, 'code'=>$code];
    }
}