<?php
// composer require sendinblue/api-v3-sdk

namespace EvoPhp\Actions\Notifications\Handlers;

use EvoPhp\Actions\Notifications\Interfaces\MailInterface;
use EvoPhp\Resources\Options;
use Sendinblue\Client\Api\TransactionalEmailsApi;
use Sendinblue\Client\Model\SendSmtpEmail;
use Sendinblue\Client\Model\SendSmtpEmailTo;
use Sendinblue\Client\Configuration;

final class BrevoHandler implements MailInterface
{
    private $api_instance;
    private $sendSmtpEmail;

    public function __construct() {
        // Initialize Brevo API key from the options
        Configuration::getDefaultConfiguration()->setApiKey('api-key', Options::get('smtp_password'));
        $this->api_instance = new TransactionalEmailsApi();
        $this->sendSmtpEmail = new SendSmtpEmail();
    }

    public function send(object $notificationObject) {
        $this->addReceivers($notificationObject->receivers);
        try {
            $this->sendSmtpEmail->setSubject(ucwords($notificationObject->subject));
            $this->sendSmtpEmail->setHtmlContent($notificationObject->messageHTML);
            $this->sendSmtpEmail->setTextContent($notificationObject->messageText);
            
            // Send email via Brevo API
            $this->api_instance->sendTransacEmail($this->sendSmtpEmail);

            $notificationObject->error = null; // Successful email send
        } catch (\Exception $e) {
            $notificationObject->error = $e->getMessage(); // Error during sending
        }
    }

    public function addReceivers($receivers) {
        $to = [];
        foreach ($receivers as $receiver) {
            $to[] = new SendSmtpEmailTo($receiver['email'], $receiver['name'] ?? '');
        }
        $this->sendSmtpEmail->setTo($to); // Set the recipients
    }
}
