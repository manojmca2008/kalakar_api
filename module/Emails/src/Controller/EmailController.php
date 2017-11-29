<?php

namespace Emails\Controller;

use MCommons\Controller\AbstractRestfulController;

class EmailController extends AbstractRestfulController {

    public function getList() {
        echo "asdsads";
        die;
        $mail = new \MCommons\Message();
        $content = 'this in test mail';
        $mail->setBody($content);
        $mail->setFrom('Freeaqingme@example.org', 'Sender\'s name');
        $mail->addTo('manoj841922@gmail.com', 'Name o. recipient');
        $mail->setSubject('TestSubject');
//$mail->addAttachment($attachment);
        $mail->Sendmail();
        print_r($mail);
        die;
        die;
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
        //$aconfig = \MCommons\StaticFunctions::getServiceLocator()->get('config');
        //$config = $this->getServiceLocator('config');
        //print_r($aconfig);die;
        //$userModel = $this->getServiceLocator(\User\Model\User::class);
//        $pubnub = new \MCommons\PubNub();
//        $chanel = "mytest_102";
//        $msg = "this is testing";
//        $res = $pubnub->publish($chanel, $msg);
//        $fileName = "images/pexels-photo.jpg";
//        $savePath = "upload/pexels-photo.jpg";
//        $imageResize = new \MCommons\ImageResize($fileName);
//        $newWidth = 50;
//        $newHeight = 50;
//        $res = $imageResize->resizeImage($newWidth, $newHeight);
//        $imageResize->saveImage($savePath);
        //print_r($res);die;
        //return $this->response($userDetails);
    }

}
