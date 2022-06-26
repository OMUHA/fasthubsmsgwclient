<?php

namespace omuha\Fasthubsmsclient\models;

class SendTextRequest
{
    public SMSChannel $channel;
    public  $messages = array();

    public function addMessage(TextMessage $textMessage)
    {
        $this->messages[] = $textMessage;
    }
}