<?php

namespace MCommons;

class PubNub {
    
    protected $pubnub;
    
    public function __construct() {
        $initialConfig = new \PubNub\PNConfiguration();
        $config = StaticFunctions::getServiceLocator()->get('config');
        $initialConfig->setSubscribeKey($config['constants']['pubnub']['subscribe_key']);
        $initialConfig->setPublishKey($config['constants']['pubnub']['publish_key']);
        $this->pubnub = new \PubNub\PubNub($initialConfig);
    }

    public function publish($chanel,$msg=[]) {
        $this->pubnub->publish()
                ->channel($chanel)
                ->message($msg)
                ->sync();
    }
    public function subscribe($chanel,$msg=[]) {
        $this->pubnub->publish()
                ->channel($chanel)
                ->message($msg)
                ->sync();   
    }
}
