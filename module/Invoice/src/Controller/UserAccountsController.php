<?php

namespace Invoice\Controller;

use Invoice\Model\UserAccounts;
use Invoice\Model\User;
use MCommons\Controller\AbstractRestfulController;

class UserAccountsController extends AbstractRestfulController {

    public function getList() {
        $userId = (int) $this->getQueryParams('userId',false);
        $userAccountModel = $this->getServiceLocator(UserAccounts::class);
        $AccountList =  $userAccountModel->getUserAccounts($userId);
        if(!empty($AccountList)){
            return $AccountList;
        }
       return [];
    }
    
    public function get($id) {
        $id = (int) $id;
        $userAccountModel = $this->getServiceLocator(UserAccounts::class);
        $userModel = $this->getServiceLocator(User::class);
        $AccountList =  $userAccountModel->getUserAccountDetails($id);
        if(!empty($AccountList)){
            $userModel->id = $AccountList->userId;
            $userModel->update(['selectedAccountId'=>$id]);
            return (array) $AccountList;
        }
       return [];
    }
    public function create($data) {
        $userAccounts = $this->getServiceLocator(UserAccounts::class);
        $accData = [
            'userId' => $data['userId'],
            'accountName' => ucfirst($data['accountName']),
            'country' => $data['country'],
            'createDate' => date('Y-m-d H:i:s'),
            'status' => 1,
        ];
        $save = $userAccounts->insert($accData);
        if ($save) {
            return $this->response(['status' => 'success']);
        } else {
            throw new \Exception("Issue in account creation", 400);
        }
    }
}
