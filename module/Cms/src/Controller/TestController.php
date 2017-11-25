<?php

namespace Cms\Controller;
use MCommons\Controller\AbstractRestfulController;

class TestController extends AbstractRestfulController {

    public function getList() {
        $config = \MCommons\StaticFunctions::getServiceLocator()->get('config');
       // pr($config);die;
        $sender = 'manoj841922@gmail.com';
        $sendername = 'manoj';
        $recievers = 'manoj.s.singhal@gmail.com';
        $template = 'email-template/user-registration';
        $layout = 'email-layout/default';
        $variables = [];
        $subject = 'this is a test mail';
        $attachment = 'upload/pexels-photo.jpg';
        
                   $layoutView = new \Zend\View\Model\ViewModel();
                   $view = new \Zend\View\Model\ViewModel();
                   $layoutView->setTemplate($layout);
                   $view->setTemplate($template);
                   
                   try {
            $renderer = \MCommons\StaticFunctions::getServiceLocator()->get('ViewRenderer');
        } catch (\Exception $ex) {
            // It is useful resque email
            $renderer = new \Zend\View\Renderer\PhpRenderer();
            $templateMaps = $config['view_manager']['template_map'];
            $resolver = new \Zend\View\Resolver\TemplateMapResolver($templateMaps);
            $renderer->setResolver($resolver);
        }

        $content = $renderer->render($view);
                   //print_r($layoutView);die;
        //s$content = $renderer->render($layoutView);
        //print_r($content);die;
$mail = new \MCommons\Message();
$mail->setBody($content);
$mail->setFrom('Freeaqingme@example.org', 'Sender\'s name');
$mail->addTo('manoj841922@gmail.com', 'Name o. recipient');
$mail->setSubject('TestSubject');
//$mail->addAttachment($attachment);
$mail->Sendmail();
        
        // $mail = \MCommons\StaticFunctions::sendMail($sender, $sendername, $recievers, $template, $layout, $variables, $subject);
         print_r($mail);die;
//echo "i m here";die;
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
