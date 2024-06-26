<?php 

namespace EvoPhp\Actions\Notifications;

use EvoPhp\Resources\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * summary
 */
class Mails
{
    /**
     * summary
     */
    public $mail;

    public $mailer = NULL;

    public $Host;

    public $Port;

    public $Username;

    public $Password;

    public $From;

    public $FromName;

    public $error = "";

    public $ee;

    public $isElastic = false;

    public function __construct()
    {
        $this->mail = new PHPMailer();
    }

    public function setCredentials() {
        $this->Host = Options::get('smtp_host');
		$this->Port = Options::get('smtp_port');
		$this->Username = Options::get('smtp_username');
		$this->Password = Options::get('smtp_password');
		$this->From = Options::get('smtp_email');
		$this->FromName = Options::get('smtp_name');
    }

    private function setMail() {
        $this->mail->isMail();
    }

    private function setSendmail() {
        $this->mail->isSendmail();
    }

    private function setQmail() {
        $this->mail->isQmail();
    }

    private function setSmtp() {
        $this->mail->isSMTP();
    }

    private function setElastic() {
        $this->isElastic = true;
    }

    private function setMailer() {
        $mailer = ($this->mailer == NULL) ? Options::get("mailer") : $this->mailer;
        if($mailer) :
            $callback = "set".ucwords($mailer);
            if(method_exists($this, $callback)) :
                $this->$callback();
            else:
                $this->setSendmail();
            endif;
        else:
            $this->setSendmail();
        endif;
    }

    public function readyMailer() {
		$emailAddress = ($option = Options::get("company_email")) ? $option : $this->From;
		$this->mail->AddAddress($emailAddress, "No Reply");
		$this->mail->SMTPAuth = true;
		$this->mail->SMTPSecure = 'ssl';
		$this->mail->Host = $this->Host;
		$this->mail->Port = $this->Port;
		$this->mail->Username = $this->Username;
		$this->mail->Password = $this->Password;
		$this->mail->From = $this->From;
		$this->mail->FromName = $this->FromName;
		$this->mail->IsHTML(true);
    }

    public function send($notificationObject) {
        if(!Options::get("activate_smtp")) return $this;
        $this->setCredentials();
        $this->setMailer();
        if($this->isElastic) {
            $this->readyElasticEmail();
            return $this->sendElasticEmail($notificationObject);
        }
        $this->readyMailer();
        try {
            $this->mail->Subject = ucwords($notificationObject->subject);
            $this->mail->Body    = $notificationObject->messageHTML;
            $this->mail->AltBody = $notificationObject->messageText;
            $this->mail->send();
            $this->error = $this->mail->ErrorInfo;
        } catch (Exception $e) {
            $this->error = $e->getMessage(); //Boring error messages from anything else!
        }
        return $this;
    }

    public function readyElasticEmail() {
        $config = \ElasticEmail\Configuration::getDefaultConfiguration()->setApiKey('X-ElasticEmail-ApiKey', $this->Password);
		$this->ee = new \ElasticEmail\Api\EmailsApi(
		    new \GuzzleHttp\Client(),
		    $config
		);
    }

    public function sendElasticEmail($notificationObject) {
        $receivers = array_map(function($i){
            return new \ElasticEmail\Model\EmailRecipient($i);
        }, $notificationObject->receivers);
        $email = new \ElasticEmail\Model\EmailMessageData(array(
		    "recipients" => $receivers,
		    "content" => new \ElasticEmail\Model\EmailContent(array(
		        "body" => array(
		            new \ElasticEmail\Model\BodyPart(array(
		                "content_type" => "HTML",
		                "content" => $notificationObject->messageHTML
		            ))
		        ),
		        "from" => $this->From,
		        "fromName" => $this->FromName,
		        "subject" => ucwords($notificationObject->subject)
		    ))
		));
		try {
		    $this->ee->emailsPost($email);
		} catch (Exception $e) {
		    $this->error = 'Exception when calling EE API: '. $e->getMessage();
		}
        return $this;
    }

}