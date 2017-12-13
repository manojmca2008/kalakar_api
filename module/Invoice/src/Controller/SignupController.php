<?php

namespace Invoice\Controller;

use MCommons\Controller\AbstractRestfulController;
use MCommons\SmsSending;
use Invoice\Model\UserAccounts;
use Invoice\Model\User;

class SignupController extends AbstractRestfulController {

    public function create($data) {
        if (isset($data['email']) && empty($data['email'])) {
            throw new \Exception("Email address can not be blank", 400);
        }
        $userModel = $this->getServiceLocator(User::class);
        $checkUser = $userModel->checkUser($data['email']);
        if (count($checkUser) > 0 && $checkUser['status'] == 1) {
            throw new \Exception("The email address is already in use by another account.", 400);
        }
        $otp = mt_rand(100000, 999999);
        $data = [
            'firstName' => isset($data['firstName']) ? ucfirst($data['firstName']) : '',
            'lastName' => isset($data['lastName']) ? ucfirst($data['lastName']) : '',
            'phone' => isset($data['phone']) ? $data['phone'] : '',
            'email' => isset($data['email']) ? $data['email'] : '',
            'password' => isset($data['password']) ? md5($data['password']) : '',
            'createDate' => date('Y-m-d H:i:s'),
            'otp' => $otp,
            'status' => 1,
        ];
        $save = $userModel->insert($data);
        if ($save) {
            //print_r($data);die;
            /*             * ******OTP send to user ************* */
            //$this->sendOtp($data['phone'],$otp);
            /*             * ******************Send user registration mail ************* */
            $data['userId'] = $userModel->id;
            $accountDetails = $this->addAccount($data);
            $this->sendMail($data);
            $userDetails = $userModel->getUserDetails($data['email']);
            $response = ['accountDetails' => (array) $accountDetails, 'userDetails' => (array) $userDetails];
            return $response;
        }
    }

    public function get($id) {
        $userModel = $this->getServiceLocator(\Invoice\Model\User::class);
        $userDetails = $userModel->getOtp($id);
        if (!empty($userDetails)) {
            $mailData = [
                'name'=>$userDetails->firstName,
                'email'=>$userDetails->email
            ];
            $this->sendWelcomeMail($mailData);
            return [
                'phone' => $userDetails->phone,
                'email' => $userDetails->email,
                'status'=>'varified'
            ];
        }else{
            return [
                'phone' => $userDetails->phone,
                'email' => $userDetails->email,
                'status'=>'failed'
            ];
        }
    }

    public function sendOtp($phone, $otp) {
        $message = "Welcome to Incred. This is your six digit (OTP) number " . $otp . " Please enter this six digit (OTP) to complete your registration at Incred. ";
        $smsdata = [
            'phone' => $phone,
            'message' => $message,
        ];
        $object = new SmsSending();
        $res = $object->sendSms($smsdata);
    }

    public function sendMail($mailData) {
        $template = 'email-template/send-otp';
        $layout = 'email-layout/default';
        $subject = 'One Time Password';

        $variables = array(
            'otp' => $mailData['otp'],
            'name' => $mailData['firstName'],
            'phone' => $mailData['phone'],
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

    public function addAccount($data) {
        $userAccounts = $this->getServiceLocator(UserAccounts::class);
        $userModel = $this->getServiceLocator(User::class);
        $accData = [
            'userId' => $data['userId'],
            'accountName' => $data['firstName'] . " " . $data['lastName'],
            'country' => 'India',
            'createDate' => $data['createDate'],
            'status' => 1,
        ];
        $save = $userAccounts->insert($accData);
        if ($save) {
            $accountDetails = $userAccounts->getUserAccountDetails($userAccounts->id);
            $userModel->id = $data['userId'];
            $update = $userModel->update(['selectedAccountId' => $userAccounts->id]);
            if (!empty($accountDetails)) {
                return $accountDetails;
            }
        } else {
            throw new \Exception("Issue in account creation", 400);
        }
    }
    public function sendWelcomeMail($mailData) {
        $template = 'email-template/welcome-incred';
        $layout = 'email-layout/default';
        $subject = 'Welcome To Incred';

        $variables = array(
            'name' => $mailData['name'],
        );
        $layoutVariables = array(
            'name' => 'manoj',
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
