<?php

namespace omuha\Fasthubsmsclient;


use Httpful\Request;
use omuha\Fasthubsmsclient\models\SendTextRequest;
use omuha\Fasthubsmsclient\models\SendTextResponse;
use omuha\Fasthubsmsclient\models\SMSChannel;
use omuha\Fasthubsmsclient\models\TextMessage;

class FastHubSMSClient
{
    private SMSChannel $channel ;
    private $deliveryReportUrl;
    protected $sourceChannel ;
    protected $smsBalance = 10;

    public function __construct($channelID, $password, $sourceChannel,
                                $remoteUrl = 'https://secure-gw.fasthub.co.tz/fasthub/messaging/json/api',
                                $deliveryReportUrl =  "https://secure-gw.fasthub.co.tz/api/dlr/request/polling/handler")
    {

        $this->channel = new SMSChannel();
        $this->channel->channel = $channelID;
        $this->channel->password = $password;

        $this->remote = $remoteUrl;
        $this->sourceChannel = $sourceChannel;
        $this->deliveryReportUrl = $deliveryReportUrl;
    }

    function SendTextMessage($message, $phoneNumber): SendTextResponse
    {
        $textMessage = new TextMessage();
        $textMessage->msisdn = $phoneNumber;
        $textMessage->text = $message;
        $textMessage->source = $this->sourceChannel;
        $msgBody = new SendTextRequest();
        $msgBody->channel = $this->channel;
        $msgBody->addMessage($textMessage);

        $sms = new SendTextResponse();
        try {

            $Response = Request::post($this->remote)
                ->sends('json')
                ->body(json_encode($msgBody))
                ->expects('json')
                ->send();
            if($Response->hasBody()){
                if ($Response->body->sms_quota > 0){
                    $sms->isSuccessful = $Response->body->isSuccessful == 1 ? 1 : 2;
                    $sms->reference_id = $Response->body->reference_id;
                    $sms->status_comment = $Response->body->error_description;
                }else{
                    $sms->sms_status = 2;
                    $sms->status_comment = "SMS Quota exceeded";
                }
            }else{
                $sms->isSuccessful = 2;
                $sms->status_comment = "GW Error, Empty Response";
            }
        } catch (\Httpful\Exception\ConnectionErrorException $e) {
            $sms->isSuccessful = 3;
            $sms->statusComment = "Message cannot be Sent, GW Error";//$e->getMessage();
        }

        return $sms;
    }

    public function getSMSBalance()
    {
        return $this->smsBalance;
    }


}