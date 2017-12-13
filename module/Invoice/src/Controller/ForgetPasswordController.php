<?php

namespace Invoice\Controller;

use MCommons\Controller\AbstractRestfulController;
use Invoice\Model\User;

class ForgetPasswordController extends AbstractRestfulController {

    public function create($data) {
        if (isset($data['email']) && empty($data['email'])) {
            throw new \Exception("Email address can not be blank", 400);
        }
        $userModel = $this->getServiceLocator(User::class);
        $checkUser = $userModel->checkUser($data['email']);
        if (count($checkUser) == 0) {
            throw new \Exception("Account Not exist with this email", 400);
        }
        $uniqidStr = md5(uniqid(mt_rand()));
        $resetPassLink = SITE_URL.'reset-password/' . $uniqidStr;
        $userModel->id = $checkUser->id;
        $update = $userModel->update(['forgetPassIdentity' => $uniqidStr]);
        $emailData = [
            'name' => $checkUser->firstName,
            'email' => $data['email'],
            'resetPassLink' => $resetPassLink,
        ];
        if ($update) {
            $this->sendMail($emailData);
            return [
                'email' => $data['email'],
                'status' => 'sent'
            ];
        }else{
            return [
                'email' => $data['email'],
                'status' => 'not sent'
            ];
        }
    }

    public function sendMail($mailData) {
        $template = 'email-template/forget-password';
        $layout = 'email-layout/default';
        $subject = 'Password Update Request';

        $variables = array(
            'name' => $mailData['name'],
            'resetPassLink' => $mailData['resetPassLink'],
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
