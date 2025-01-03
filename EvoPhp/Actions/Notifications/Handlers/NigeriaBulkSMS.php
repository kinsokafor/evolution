<?php

namespace EvoPhp\Actions\Notifications\Handlers;

use EvoPhp\Actions\Notifications\Interfaces\TextMessageInterface;
use EvoPhp\Api\Operations;
use EvoPhp\Resources\Options;

final class NigeriaBulkSMS implements TextMessageInterface
{
    public $username;

    public $password;

    public $sender = "";

    public function __construct() {
        $this->username = Options::get("sms_username") ?? "";
        $this->password = Options::get("sms_password") ?? "";
        $this->sender = Options::get("sms_sender") ?? "";
    }

    public function send(object $notificationObject) {
        $url = "https://portal.nigeriabulksms.com/api/";
        $data = [
            "username" => $this->username,
            "password" => $this->password,
            "sender" => $this->sender,
            "message" => $notificationObject->messageText,
            "mobiles" => self::processMobiles($notificationObject->receivers)
        ];
        $data = http_build_query($data);
        $res = Operations::callAPI($url."?".$data);
        if(isset($res->error)) {
            $notificationObject->error = $res->error;
        }
        return $res;
    }

    private static function processMobiles($receivers) {
        return preg_replace("/,+/", ",", implode(",", array_map(function($i) {
            return $i["phone"] ?? "";
        }, $receivers)));
    }

    public function action($action = "balance", ...$otherParams) {
        $url = "https://portal.nigeriabulksms.com/api/";
        $data = [
            "username" => $this->username,
            "password" => $this->password,
            "action" => $action,
            ...$otherParams
        ];
        $data = http_build_query($data);
        return Operations::callAPI($url."?".$data);
    } 

    public function balance(...$params) {
        return $this->action("balance", ...$params);
    }

    public function profile(...$params) {
        return $this->action("profile", ...$params);
    }

    public function history(...$params) {
        return $this->action("history", ...$params);
    }

    public function reports(...$params) {
        return $this->action("reports", ...$params);
    }

    public function payments(...$params) {
        return $this->action("payments", ...$params);
    }
}