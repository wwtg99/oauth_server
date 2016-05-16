<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/19
 * Time: 14:33
 */

namespace Flight2wwu\Component\Utils;
use Flight2wwu\Common\ServiceProvider;


/**
 * Class Mail
 * Send mail depends on SwiftMail
 * @package Flight2wwu\Component\Utils
 */
class Mail implements ServiceProvider
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $content_type = 'text/html';

    /**
     * @var string
     */
    private $date;

    /**
     * Mail constructor.
     */
    function __construct()
    {
        $this->loadConfig([]);
    }

    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $conf = \Flight::get('mail');
        $this->loadConfig($conf);
    }

    /**
     * @param array $conf
     */
    public function loadConfig(array $conf)
    {
        $method = 'mail';
        $params = [];
        if ($conf && is_array($conf)) {
            $method = isset($conf['method']) ? $conf['method'] : 'mail';
            $params = isset($conf['params']) ? $conf['params'] : [];
        }
        $this->mailer = self::getMail($method, $params);
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param \Swift_Mailer $mailer
     * @return Mail
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     * @return Mail
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * @param string $content_type
     * @return Mail
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     * @return Mail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return Mail
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @param array|\Swift_Message $message
     * @return int|null
     */
    public function send($message)
    {
        if (is_array($message)) {
            $subject = array_key_exists('subject', $message) ? $message['subject'] : $this->subject;
            $from = array_key_exists('from', $message) ? $message['from'] : $this->from;
            $to = $message['to'];
            $body = $message['body'];
            $ctype = array_key_exists('content_type', $message) ? $message['content_type'] : $this->content_type;
            $msg = \Swift_Message::newInstance($subject, $body, $ctype, 'UTF-8')->setFrom($from)->setTo($to);
            if (array_key_exists('id', $message)) {
                $msg->setId($message['id']);
            }
            if (array_key_exists('cc', $message)) {
                $msg->setCc($message['cc']);
            }
            if (array_key_exists('bcc', $message)) {
                $msg->setBcc($message['bcc']);
            }
            if (array_key_exists('reply-to', $message)) {
                $msg->setReplyTo($message['reply-to']);
            }
            $date = array_key_exists('date', $message) ? $message['date'] : $this->date;
            if ($date) {
                $msg->setDate($message['date']);
            }
            return $this->mailer->send($msg);
        } elseif ($message instanceof \Swift_Message) {
            return $this->mailer->send($message);
        } else {
            return null;
        }
    }

    /**
     * @param string $type
     * @param array $param
     * @return \Swift_Mailer
     */
    public static function getMail($type = 'mail', $param = [])
    {
        switch($type) {
            case 'smtp':
                $host = array_key_exists('host', $param) ? $param['host'] : 'localhost';
                $port = array_key_exists('port', $param) ? $param['port'] : 25;
                $security = array_key_exists('security', $param) ? $param['security'] : null;
                $tran = \Swift_SmtpTransport::newInstance($host, $port, $security)->setUsername($param['username'])->setPassword($param['password']);
                break;
            case 'sendmail':
                $cmd = array_key_exists('command', $param) ? $param['command'] : '/usr/sbin/sendmail -bs';
                $tran = \Swift_SendmailTransport::newInstance($cmd);
                break;
            case 'mail':
            default:
                $tran = \Swift_MailTransport::newInstance();
                break;
        }
        return \Swift_Mailer::newInstance($tran);
    }

} 