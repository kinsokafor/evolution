<?php

namespace EvoPhp\Actions\Notifications\Handlers;

use EvoPhp\Actions\Notifications\Interfaces\MailInterface;
use EvoPhp\Resources\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// use PHPMailer\PHPMailer\SMTP;

final class PhpMailerHandler implements MailInterface
{
    public $mail;

    public function __construct() {
        $this->mail = new PHPMailer();
        $emailAddress = ($option = Options::get("company_email")) ? $option : Options::get('smtp_email');
        $this->mail->AddAddress($emailAddress, "No Reply");
        $this->mail->SMTPAuth = true;
        $this->mail->Host = Options::get('smtp_host');
        $this->mail->Port = Options::get('smtp_port');
        $this->mail->Username = Options::get('smtp_username');
        $this->mail->Password = Options::get('smtp_password');
        $this->mail->setFrom(Options::get('smtp_email'), Options::get('smtp_name'));
        $this->mail->addReplyTo(Options::get('smtp_email'), Options::get('smtp_name'));
        $this->mail->IsHTML(true);
    }

    public function send(object $notificationObject) {
        $this->addReceivers($notificationObject->receivers);
        try {
            $this->mail->Subject = ucwords($notificationObject->subject);
            $this->mail->Body    = $notificationObject->messageHTML;
            $this->mail->AltBody = $notificationObject->messageText;
            $this->mail->send();
            $notificationObject->error = $this->mail->ErrorInfo;
        } catch (Exception $e) {
            $notificationObject->error = $e->getMessage(); //Boring error messages from anything else!
        }
    }

    public function addReceivers($receivers) {
        foreach ($receivers as $receiver) {
            $this->mail->addBCC($receiver['email'], $receiver['name'] ?? '');
        }
    }
}