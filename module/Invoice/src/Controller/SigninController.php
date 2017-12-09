<?php

namespace Invoice\Controller;

use MCommons\Controller\AbstractRestfulController;
use Invoice\Model\User;
use Invoice\Model\UserAccounts;

class SigninController extends AbstractRestfulController {

    public function create($data) {
        if (isset($data['email']) && empty($data['email'])) {
            throw new \Exception("Email Id Can Not Be Null", 400);
        }
        $userModel = $this->getServiceLocator(User::class);
        $checkUser = $userModel->checkUser($data['email']);
        if (count($checkUser) == 0) {
            throw new \Exception("Account Not exist with this email", 400);
        }
        $userAccounts = $this->getServiceLocator(UserAccounts::class);
        $userDetails = $userModel->getUserDetails($data['email']);
        $userModel->id = $userDetails->id;
        $accountDetails = $userAccounts->getUserAccountDetails($userDetails->selectedAccountId);
        $response = ['accountDetails' => (array) $accountDetails, 'userDetails' => (array) $userDetails];
        return $response;
    }       
}
