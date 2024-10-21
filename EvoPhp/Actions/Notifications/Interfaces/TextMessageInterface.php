<?php

namespace EvoPhp\Actions\Notifications\Interfaces;

interface TextMessageInterface {
    public function send(object $notificationObject);
}