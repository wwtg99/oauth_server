<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/7
 * Time: 18:12
 */

namespace App\Controller;


use App\Model\Tool;
use Flight2wwu\Component\Utils\FormatUtils;

class ToolController extends TokenController
{

    public static function send_email()
    {
        $re = self::check('send_email');
        if (is_array($re)) {
            \Flight::json($re);
            return false;
        } else {
            $re = self::checkInputs(['to', 'subject', 'body'], []);
            if ($re === true) {
                $to = self::getInput('to');
                if (strpos($to, ',') > 0) {
                    $to = explode(',', $to);
                }
                $subject = self::getInput('subject');
                $body = self::getInput('body');
                $msg = self::getArrayInputN(['cc', 'bcc']);
                $re = Tool::sendEmail($to, $subject, $body, $msg);
                if ($re) {
                    \Flight::json(['result'=>'send email successful']);
                } else {
                    \Flight::json(['error'=>['message'=>'send email failed', 'code'=>2]]);
                }
            } else {
                \Flight::json(['error'=>$re]);
            }
            return false;
        }
    }

    public static function express_info()
    {
        $re = self::checkInputs(['company', 'no'], []);
        if ($re === true) {
            $com = trim(self::getInput('company'));
            $no = trim(self::getInput('no'));
            $type = self::getInput('type');
            $info = Tool::expressInfo($com, $no);
            if ($type == 'json') {
                \Flight::json($info);
            } else {
                echo FormatUtils::arrayToString($info);
            }
        } else {
            \Flight::json(['error'=>$re]);
        }
        return false;
    }
}