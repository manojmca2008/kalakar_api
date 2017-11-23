<?php

namespace User\Controller;

use MCommons\Controller\AbstractRestfulController;

class RegisterController extends AbstractRestfulController {

    public function create($data) {
        if(isset($data['email']) && empty($data['email'])){
            return $this->response(['error_msg'=>"Email Id Can Not Be Null"]);
        }
        $userModel = $this->getServiceLocator(\User\Model\User::class);
        $userDetails = $userModel->checkUser($data['email']);
        if(count($userDetails) > 0){
            return $this->response(['error_msg'=>"Account Already exist with this email"]);
        }
        $data = [
            'first_name'=>$data['first_name'],
            'last_name'=>$data['last_name'],
            'user_name'=>$data['username'],
            'photo_url'=>$data['photo_url'],
            'user_source'=>$data['user_source'],
            'mobile'=>$data['mobile'],
            'email'=>$data['email'],
            'password'=>md5($data['password']),
        ];
        $save = $userModel->insert($data);
        if($save){
            return $this->response(['status'=>'success']);
        }
    }
}
