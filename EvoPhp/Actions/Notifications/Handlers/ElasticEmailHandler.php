<?php

namespace EvoPhp\Actions\Notifications\Handlers;

use EvoPhp\Actions\Notifications\Interfaces\MailInterface;
use EvoPhp\Resources\Options;
use ElasticEmail\Configuration;
use ElasticEmail\Api\EmailsApi;
use GuzzleHttp\Client;
use ElasticEmail\Model\EmailRecipient;
use ElasticEmail\Model\EmailMessageData;
use ElasticEmail\Model\EmailContent;
use ElasticEmail\Model\BodyPart;

final class ElasticEmailHandler implements MailInterface
{
    public $mail;

    public function __construct() {
        $config = Configuration::getDefaultConfiguration()->setApiKey('X-ElasticEmail-ApiKey', Options::get('smtp_password'));
        $this->mail = new EmailsApi(
            new Client(),
            $config
        );
    }

    public function send(object $notificationObject) {
        $receivers = array_map(function($i){
            return new EmailRecipient($i);
        }, $notificationObject->receivers);
        $email = new EmailMessageData(array(
		    "recipients" => $receivers,
		    "content" => new EmailContent(array(
		        "body" => array(
		            new BodyPart(array(
		                "content_type" => "HTML",
		                "content" => $notificationObject->messageHTML
		            ))
		        ),
		        "from" => Options::get('smtp_email'),
		        "fromName" => Options::get('smtp_name'),
		        "subject" => ucwords($notificationObject->subject)
		    ))
		));
		try {
		    $this->mail->emailsPost($email);
		} catch (\Exception $e) {
		    $notificationObject->error = 'Exception when calling EE API: '. $e->getMessage();
		}
        return $this;
    }
}