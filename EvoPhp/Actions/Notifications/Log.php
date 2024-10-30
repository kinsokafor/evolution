<?php 

namespace EvoPhp\Actions\Notifications;

use EvoPhp\Database\Query;
use EvoPhp\Database\Session;
use EvoPhp\Api\Operations;
use EvoPhp\Resources\User;

/**
 * summary
 */
class Log
{
    private $data = [];

    public $query;
    /**
     * summary
     */
    public function __construct()
    {
        $this->query = new Query;
    }

    public static function createTable() {
        $self = new self;

        $statement = "CREATE TABLE IF NOT EXISTS notification (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) NOT NULL,
            content TEXT NOT NULL,
            type VARCHAR(50) NOT NULL,
            action TEXT NOT NULL,
            max_send INT UNSIGNED,
            current_send INT UNSIGNED,
            last_sent BIGINT(20) NOT NULL,
            send_interval BIGINT(20) NOT NULL,
            status VARCHAR(20) NOT NULL,
            INDEX idx_user_id (user_id)
            )";
        $self->query->query($statement)->execute();
    }

    public static function maintainTable() {
        $self = new self();
        $statement = "ALTER TABLE notification
                        MODIFY COLUMN last_sent BIGINT(20),
                        MODIFY COLUMN send_interval BIGINT(20)";
        $self->query->query($statement)->execute();
    }

    private function prepareData($notificationObject) {
        $data = $notificationObject->receivers;
        if(Operations::count($data)) :
            foreach ($data as $u) {
                if(!$u['id']) continue;
                $thisData = [
                    "user_id" => $u['id'],
                    "content" => $notificationObject->messageText,
                    "type" => $notificationObject->type,
                    "action" => $notificationObject->action,
                    "max_send" => $notificationObject->maxSend,
                    "current_send" => 1,
                    "last_sent" => time(),
                    "send_interval" => $notificationObject->sendInterval,
                    "status" => "unread"
                ];
                array_push($this->data, $thisData);
            }
        endif;
    }

    public function log($notificationObject) {
        $this->prepareData($notificationObject);
        if(!Operations::count($this->data)) return false;
        $this->query->insert("notification", "isssiiiis", ...$this->data)->execute();
        return true;
    }
}