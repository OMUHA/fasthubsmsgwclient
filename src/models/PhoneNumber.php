<?php

namespace omuha\Fasthubsmsclient\models;

class PhoneNumber
{
    public $countryCode;
    public $contactNumber;
    public $contactName ;

    public function __construct($countryCode,$contactNumber, $contactName){

    }

    public function __toString(){
        return $this->contactNumber;
    }
}