<?php

namespace Test\Controller;

use MCommons\Controller\AbstractRestfulController;
use MCommons\FormProcessor;

class TestController extends AbstractRestfulController {

    public function getList() {
        $template = 'email-template/user-registration';
        $layout = 'email-layout/default';
        $subject = 'this is a test mail';

        $variables = array(
            'name' => 'manoj',
            'phone' => 9716835598,
            'email' => 'manoj841922@gmail.com',
            'message' => 'this is a test mail',
        );
        $layoutVariables = array(
            'name' => 'manoj',
            'phone' => 9716835598,
            'email' => 'manoj841922@gmail.com',
            'message' => 'this is a test mail',
        );
        $data = array(
            'receiver' => ['manoj.s.singhal@gmail.com'],
            'sender' => 'manoj841922@gmail.com',
            'senderName' => 'manoj singhal',
            'template' => $template,
            'layout' => $layout,
            'subject' => $subject,
            'variables' => $variables,
            'layoutVariables' => $layoutVariables
        );
        $mail = \MCommons\StaticFunctions::sendMail($data);
        print_r($mail);
        die;
    }

    public function create($data) {
        $form = new FormProcessor();
        $processData = $form->processFormData($data);
        return $processData;
    }

}
