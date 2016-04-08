<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/7
 * Time: 18:32
 */

namespace App\Model;


use Flight2wwu\Component\Utils\Express;
use Flight2wwu\Component\Utils\Mail;

class Tool
{

    /**
     * @param string|array $to
     * @param string $subject
     * @param string $body
     * @param array $message
     * @return int|null
     */
    public static function sendEmail($to, $subject, $body, $message = [])
    {
        $mail = \Flight::Mail();
        if ($mail instanceof Mail) {
            $mail->setFrom('no-reply@genowise.com');
            $message['to'] = $to;
            $message['subject'] = $subject;
            $message['body'] = $body;
            return $mail->send($message);
        }
        return null;
    }

    /**
     * @param string $company
     * @param string $no
     * @return array
     */
    public static function expressInfo($company, $no)
    {
        $exp = \Flight::Express();
        if ($exp instanceof Express) {
            $info = $exp->track($company, $no);
            return $info;
        }
        return [];
    }
}