<?php

namespace Cms\Controller;

use MCommons\Controller\AbstractRestfulController;

class SigninController extends AbstractRestfulController {

    public function create($data) {
        if (isset($data['email']) && empty($data['email'])) {
            throw new \Exception("Email Id Can Not Be Null", 400);
        }
        $userModel = $this->getServiceLocator(\Cms\Model\User::class);
        $userDetails = $userModel->checkUser($data['email']);
        //print_r($userDetails);die;
        if ($userDetails['status'] == 0) {
            throw new \Exception("Your Account is inactive so contact to administrator", 400);
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
