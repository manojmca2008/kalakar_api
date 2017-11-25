<?php

namespace Cms\Controller;

use MCommons\Controller\AbstractRestfulController;

class LoginController extends AbstractRestfulController {

    public function create($data) {
        if (isset($data['email']) && empty($data['email'])) {
            return ['error_msg' => "Email Id Can Not Be Null"];
        }
        $userModel = $this->getServiceLocator(\User\Model\User::class);
        $userDetails = $userModel->checkUser($data['email']);
        if ($userDetails->status == 0) {
            return ['error_msg' => "Your Account is inactive so contact to administrator"];
        }
        $userModel->id = $userDetails->id;
        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format("Y-m-d H:i:s");
        $data = [
            'update_at'=>$currentDateTime,
        ];
        $update = $userModel->update($data);
        if($update){
            return $this->response(['status' => 'success']);
        }
    }
}
