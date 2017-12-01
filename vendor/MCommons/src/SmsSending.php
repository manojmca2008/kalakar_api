<?php

namespace MCommons;

use MCommons\StaticFunctions;

class SmsSending {

    protected $sms;
    protected $username;
    protected $password;
    protected $sender;
    protected $url;

    public function __construct() {
        $config = StaticFunctions::getServiceLocator()->get('config');
        $this->username = $config['constants']['valuefirst']['username'];
        $this->password = $config['constants']['valuefirst']['password'];
        $this->sender = $config['constants']['valuefirst']['sender'];
        $this->url = $config['constants']['valuefirst']['url'];
    }

    public function sendSms($data) {
        $number = urlencode($data['phone']);
        $sender = urlencode($this->sender);
        $message = rawurlencode($data['message']);
        $url = $this->url . '?username=' . $this->username . '&password=' . $this->password . '&to=' . $number . '&from=' . $sender . '&text=' . $message . '&dlr-mask=19&dlr-url&category=bulk';
        $response = StaticFunctions::fetchDataFromUrl($url);
        return $response;
    }

}
