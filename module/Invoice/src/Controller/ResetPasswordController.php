<?php

namespace Invoice\Controller;

use MCommons\Controller\AbstractRestfulController;
use Invoice\Model\User;

class ResetPasswordController extends AbstractRestfulController {

    public function create($data) {
        if (isset($data['password']) && empty($data['password'])) {
            throw new \Exception("New password can not be blank", 400);
        }
        $userModel = $this->getServiceLocator(User::class);
        $checkUser = $userModel->checkUserIdentity($data['resetCode']);
        if (count($checkUser) == 0) {       
            throw new \Exception("Your reset password link has expired", 400);
        }
        $userModel->id = $checkUser->id;
        $update = $userModel->update(['forgetPassIdentity' => '','password'=>md5($data['password'])]);
        if ($update) {
            return [
                'status' => 'Password has been successfully updated'
            ];
        }else{
            return [
                'email' => $data['email'],
                'status' => 'Password has not been updated'
            ];
        }
    }
}
