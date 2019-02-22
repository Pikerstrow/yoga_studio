<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 20.02.2019
 * Time: 19:20
 */

namespace Core\Support;

use Core\Exceptions\MailException;

class Mailer
{
    private $recipient;
    private $subject;
    private $header;
    private $message;

    public function __construct()
    {
        $this->setMailHeader();
    }

    public function setRecipient($value) :void
    {
        $this->recipient = $value;
    }

    public function setSubject($value) :void
    {
        $this->subject = $value;
    }

    public function setCustomHeader($value) :void
    {
        $this->header = $value;
    }

    public function setMessage($value) :void
    {
        $this->message = $value;
    }

    public function send()
    {
        if(mail($this->recipient, $this->subject, $this->message, $this->header)){
            return true;
        } else {
            throw new MailException("Помилка відправки email повідомлення!");
        }
    }

    public function setMailHeader() :void
    {
        $domen = substr(APP_URL, strpos(APP_URL, "ht"));

        $header  = "From: admin@" . $domen . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=utf-8';
        $this->header = $header;
    }
}