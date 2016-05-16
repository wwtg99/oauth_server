<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/5/10
 * Time: 14:55
 */

namespace Flight2wwu\Common;


use App\Model\Message;

class FWException extends \Exception
{

    /**
     * FWException constructor.
     * @param string|array|Message $message
     * @param int $code
     */
    public function __construct($message = '', $code = 0)
    {
        if ($message instanceof Message) {
            $code = $message->getCode();
            $message = $message->getMsg();
        } elseif (is_array($message)) {
            if (array_key_exists('message', $message)) {
                $code = isset($message['code']) ? $message['code'] : 0;
                $message = $message['message'];
            } elseif (count($message) == 3) {
                //database error
                $code = $message[0];
                $message = $message[2];
            }
        }
        parent::__construct($message, $code);
    }
}