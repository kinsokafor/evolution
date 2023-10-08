<?php 

namespace EvoPhp\Actions\Notifications;

use EvoPhp\Api\Operations;
use EvoPhp\Resources\User;
use EvoPhp\Actions\Notifications\Mails;
use EvoPhp\Actions\Notifications\Log;
use EvoPhp\Themes\Templates;
use function strip_tags;
use function str_replace;

/**
 * summary
 */
class Notifications
{
    /**
     * summary
     */
    public $messageRaw;

    public $messageText;

    public $messageHTML;

    public $receivers = [];

    public $error = "";

    public $type = "info";
    
    public $action = "#";

    public $maxSend = 1;

    public $sendInterval = 3600;

    public $subject;

    public $message;

    public $mail;

    public $data;

    public function __construct(string $message = "", string $subject = "", array $data = [])
    {
        $this->messageRaw = $message;
        $this->subject = $subject;
        $this->message = $message;
        $this->messageHTML = $message;
        $this->data = $data;
        $this->clean();
        $this->mail = new Mails;
    }

    private function clean() {
        $this->messageText = strip_tags(str_replace('<', ' <', $this->messageRaw));
    }
    
    /**
     * to
     * method used to specify the receivers of notification
     * @param  mixed $receiver accepts user_id, username, email address, phone number, array of users object or arrays,
     * object of user meta and array of user meta
     * @param  mixed $name optional fullname of the receiver to be overriden if the user is a registered user
     * with the registered user fullname.
     * @return object
     */
    public function to($receiver, $name = "") {
        switch (gettype($receiver)) {
            case "string":
            case "integer":
                if(filter_var($receiver, FILTER_VALIDATE_EMAIL)) {
                    $user = new User;
                    $meta = $user->get($receiver);
                    $id = $meta ? $meta->id : false;
                    $name = $meta ? Operations::getFullname($meta) : $name;
                    $phone = $meta ? $meta->phone : "";
                    $this->add($id, $name, $receiver, $phone);
                }
                else if(Operations::validatePhoneNumber($receiver)) {
                    $this->add(false, $name, "", $receiver);
                } else {
                    $user = new User;
                    $meta = $user->get($receiver);
                    if($meta) {
                        $this->add($meta->id, Operations::getFullname($meta), $meta->email, $meta->phone); 
                    }
                }
                break;

            case "array":
                if(Operations::count($receiver)) :
                    foreach($receiver as $r) :
                        if(gettype($r) == "object") :
                            $name = Operations::getFullname($r);
                            $email = $r->email ?? "";
                            $phone = $r->phone ?? "";
                            $id = $r->id ?? false;
                        elseif(gettype($r) == "array") :
                            $name = Operations::getFullname($r);
                            $email = $r['email'] ?? "";
                            $phone = $r['phone'] ?? "";
                            $id = $r['id'] ?? false;
                        elseif(gettype($r) == "string" || gettype($r) == 'integer') :
                            if(filter_var($r, FILTER_VALIDATE_EMAIL)) {
                                $name = Operations::getFullname($r);
                                $email = $r;
                                $phone = "";
                                $id = false;
                            }
                            else if(Operations::validatePhoneNumber($r)) {
                                $this->add(false, "", "", $r);
                            }
                            else {}
                        else:
                            $name = Operations::getFullname($receiver);
                            $email = $receiver['email'] ?? "";
                            $phone = $receiver['phone'] ?? "";
                            $id = $receiver['id'] ?? false;
                            $this->add($id, $name, $email, $phone);
                            break;
                        endif;
                        $this->add($id, $name, $email, $phone);
                    endforeach;
                endif;
                break;

            case "object":
                $name = Operations::getFullname($receiver);
                $email = $receiver->email ?? "";
                $phone = $receiver->phone ?? "";
                $id = $receiver->id ?? false;
                $this->add($id, $name, $email, $phone);
                break;

            default:
                break;
        }
        return $this;
    }

    public function toRole(...$role) {
        $user = new User;
        $users = $user->getUser()->where("role", $role)->where("status", "active")->execute();
        $this->to($users);
        return $this;
    }

    private function add($id, $name, $email = "", $phone = "") {
        if($email != "") {
            $this->mail->mail->addBCC($email, $name);
        }
        $data = ["id" => $id, "name" => $name, "email" => $email, "phone" => $phone];
        array_push($this->receivers, $data);
        return $this;
    }

    public function action($url = "#") {
        $this->action = $url;
        return $this;
    }

    public function mail() {
        $mail = $this->mail->send($this);
        $this->error = $mail->error;
        return $this;
    }

    public function log() {
        $log = new Log;
        $log->log($this);
        return $this;
    }

    public function template($template = 'email', $path = './Public/Templates') {
        $t = new Templates();
        $data = [
            'mail_content' => $this->message,
            'mail_subject' => $this->subject,
            'mail_receivers' => $this->receivers
        ];
        $data = array_merge($data, $this->data);
        $this->messageHTML = $t->get($template, $path, $data) ?: $this->messageHTML;
        return $this;
    }
    
}