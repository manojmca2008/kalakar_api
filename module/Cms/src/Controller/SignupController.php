<?php

namespace Cms\Controller;

use MCommons\Controller\AbstractRestfulController;

class SignupController extends AbstractRestfulController {

    public function create($data) {        
        if(isset($data['email']) && empty($data['email'])){
            return $this->response(['error_msg'=>"Email Id Can Not Be Null"]);
        }
        $userModel = $this->getServiceLocator(\User\Model\User::class);
        $userDetails = $userModel->checkUser($data['email']);
        if(count($userDetails) > 0 && $userDetails['status'] == 1){
            return $this->response(['error_msg'=>"Account Already exist with this email"]);
        }
        $data = [
            'first_name'=> isset($data['first_name']) ? $data['first_name'] : '',
            'last_name'=> isset($data['last_name']) ? $data['last_name'] : '',
            'user_name'=> isset($data['username']) ? $data['username'] : '',
            'photo_url'=> isset($data['photo_url']) ? $data['photo_url'] : '',
            'user_source'=> isset($data['user_source']) ? $data['user_source'] : '',
            'mobile'=> isset($data['mobile']) ? $data['mobile'] : '',
            'email'=> isset($data['email']) ? $data['email'] : '',
            'password'=> isset($data['password']) ? md5($data['password']) : '',
        ];
        $save = $userModel->insert($data);
        if($save){
            return $this->response(['status'=>'success']);
        }
    }
    
}
