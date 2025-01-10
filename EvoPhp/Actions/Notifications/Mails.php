<?php 

namespace EvoPhp\Actions\Notifications;

use EvoPhp\Actions\Notifications\Interfaces\MailInterface;
use EvoPhp\Actions\Notifications\Handlers\PhpMailerHandler;


/**
 * summary
 */
class Mails
{
    /**
     * summary
     */
    public $handler;

    public function __construct(MailInterface | NULL $handler = NULL){
        if($handler == NULL) {
            $this->handler = new PhpMailerHandler;
        } else {
            $this->handler = $handler;
        }
    }

    public function send($notificationObject) {
        return $this->handler->send($notificationObject);
    }

}