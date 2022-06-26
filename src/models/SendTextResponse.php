<?php

namespace omuha\Fasthubsmsclient\models;

class SendTextResponse
{
    public int $smsStatus;
    public string $statusComment;
    public int $isSuccessful;
    public $status_comment;
}