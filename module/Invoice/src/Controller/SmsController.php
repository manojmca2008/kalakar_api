<?php

namespace Invoice\Controller;

use MCommons\Controller\AbstractRestfulController;
use MCommons\SmsSending;
use Invoice\Model\User;

class SmsController extends AbstractRestfulController {

    public function getList() {
        
    }

    public function create($data) {
        $otp = mt_rand(100000, 999999);
        $message = "Welcome to Incred. This is your six digit otp number " . $otp . " Please enter this six digit otp to complete your registration at Incred. ";
        $smsData = [
            'phone' => $data['phone'],
            'message' => $message,
            'otp' => $otp,
            'email'=>$data['email'],
        ];
        $object = new SmsSending();
        $response = $object->sendSms($smsData);
        //$this->sendMail($smsData);
        if ($response) {
            $userModel = $this->getServiceLocator(User::class);
            $userModel->id = $data['userId'];
            $update = $userModel->update(['otp'=>$otp]);
            return ['phone' => $data['phone']];
        }
    }
    public function sendMail($mailData) {
        $template = 'email-template/send-otp';
        $layout = 'email-layout/default';
        $subject = 'One Time Password';

        $variables = array(
            'otp' => $mailData['otp'],
        );
        $layoutVariables = array(
            'name' => 'manoj',
            'phone' => 9716835598,
            'email' => 'manoj841922@gmail.com',
            'message' => 'this is a test mail',
        );
        $data = array(
            'receiver' => [$mailData['email']],
            'sender' => 'manoj841922@gmail.com',
            'senderName' => 'Incred',
            'template' => $template,
            'layout' => $layout,
            'subject' => $subject,
            'variables' => $variables,
            'layoutVariables' => $layoutVariables
        );
        $mail = \MCommons\StaticFunctions::sendMail($data);
    }

}
