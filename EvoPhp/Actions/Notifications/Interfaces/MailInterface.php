<?php

namespace EvoPhp\Actions\Notifications\Interfaces;

interface MailInterface {
    public function send(object $notificationObject);
}