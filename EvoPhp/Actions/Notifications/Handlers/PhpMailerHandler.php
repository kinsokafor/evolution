<?php

namespace EvoPhp\Actions\Notifications\Handlers;

use EvoPhp\Actions\Notifications\Interfaces\MailInterface;
use EvoPhp\Resources\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class PhpMailerHandler implements MailInterface
{
    private $mail;

    public function __construct(PHPMailer $mailer = null) {
        $this->mail = $mailer ?? new PHPMailer(true); // Inject or initialize PHPMailer
        $this->configureMailer();
    }

    private function configureMailer() {
        $this->mail->isHTML(true); // Set email format to HTML
        
        $mailMethod = Options::get('mailer'); // Fetch the configured mail method

        // Configure transport method based on the setting
        switch ($mailMethod) {
            case 'smtp':
                $this->configureSmtp();
                break;
            case 'sendmail':
                $this->configureSendmail();
                break;
            case 'mail':
            default:
                $this->configureMail();
                break;
        }

        // Common configurations
        $this->mail->setFrom(Options::get('smtp_email'), Options::get('smtp_name'));
        $this->mail->addReplyTo(Options::get('smtp_email'), Options::get('smtp_name'));
    }

    private function configureSmtp() {
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->Host = Options::get('smtp_host');
        $this->mail->Port = Options::get('smtp_port') ?? 587;
        $this->mail->Username = Options::get('smtp_username');
        $this->mail->Password = Options::get('smtp_password');
    }

    private function configureSendmail() {
        $this->mail->isSendmail();
        $this->mail->Sendmail = Options::get('sendmail_path') ?? '/usr/sbin/sendmail';
    }

    private function configureMail() {
        $this->mail->isMail();
    }

    public function send(object $notificationObject) {
        $this->addReceivers($notificationObject->receivers);
        try {
            $this->mail->Subject = ucwords($notificationObject->subject);
            $this->mail->Body = $notificationObject->messageHTML;
            $this->mail->AltBody = $notificationObject->messageText;
            $this->mail->send();
        } catch (Exception $e) {
            $notificationObject->error = $e->getMessage();
        }
    }

    private function addReceivers($receivers) {
        foreach ($receivers as $receiver) {
            if (isset($receiver['email'])) {
                $this->mail->addBCC($receiver['email'], $receiver['name'] ?? '');
            }
        }
    }
}
