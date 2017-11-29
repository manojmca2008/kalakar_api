<?php

namespace Cms\Controller;

use Cms\Model\User;
use MCommons\Controller\AbstractRestfulController;

class UserController extends AbstractRestfulController {

    public function getList() {
        $userModel = $this->getServiceLocator(User::class);
        $usersList =  $userModel->getUsers();
        if(!empty($usersList)){
            return $usersList;
        }
       return [];
    }

    public function get($id) {
        $id = (int) $id;
        $userModel = $this->getServiceLocator(User::class);
        $userDetails = $userModel->getUserDetails($id);
        if (!empty($userDetails)) {
            return $userDetails;
        }
        return [];
    }
}
