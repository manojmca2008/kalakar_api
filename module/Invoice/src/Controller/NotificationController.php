<?php

namespace Invoice\Controller;

use MCommons\Controller\AbstractRestfulController;
use MCommons\SmsSending;
use User\Model\PubnubNotification;

class NotificationController extends AbstractRestfulController {

    public function get($id) {
        $object = $this->getServiceLocator(PubnubNotification::class);
        $notifications = $object->getNotifications((int)$id);
        return ($notifications) ? $notifications : [];
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
