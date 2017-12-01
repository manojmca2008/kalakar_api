<?php

namespace Invoice\Controller;

use MCommons\Controller\AbstractRestfulController;
use MCommons\SmsSending;

class SmsController extends AbstractRestfulController {

    public function getList() {
        
    }

    public function create($data) {
        $otp = mt_rand(100000, 999999);
        $message = "Welcome to Incred. This is your six digit otp number " . $otp . " Please enter this six digit otp to complete your registration at Incred. ";
        $data = [
            'phone' => $data['phone'],
            'message' => $message,
        ];
        $object = new SmsSending();
        $response = $object->sendSms($data);
        if ($response) {
            return ['phone' => $data['phone']];
        }
    }

}
