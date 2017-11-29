<?php

namespace Test\Controller;

use MCommons\Controller\AbstractRestfulController;
use MCommons\FormProcessor;

class TestController extends AbstractRestfulController {

    public function getList() {
        echo "asass";
        die;
    }

    public function create($data) {
          $form = new FormProcessor();
          $processData = $form->processFormData($data);
          return $processData;
//        $errors = [];
//        $formData = [];
//        foreach ($data as $field => $data) {
//            $replace = ['required','-'];
//            $colums = str_replace($replace, '', $field);
//            $field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", " ", $field));
//            //echo $field;die;
//            $formData[$colums] = $data; 
//            if ((strstr($field, 'required')) && (empty($data))) {
//                $field = ucwords(str_replace('required', '', $field));
//                $errors[] = "A required field was left blank: $field ";
//                throw new \Exception("A required field was left blank: $field ", 400);
//            }
//            if ((strstr($field, 'email')) && (!empty($data)) && (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $data))) {
//                $errors[] = "This email address is not valid: <strong>$data</strong>";
//            }
//            // Error checking on select boxes when the defailt is "didnotchoose" (see README)
//            if ((is_array($data)) && (in_array('didnotchoose', $data)) && count($data) == 1) {
//                $field = ucwords(str_replace('required', '', $field));
//                $errors[] = "A required field was left blank: $field";
//                throw new \Exception("A required field was left blank: $field", 400);
//            }
//        }
//        if(empty($errors)){
//            return $formData;
//        }else{
//            throw new \Exception($errors, 400);
//            ///return $errors;
//        }
    }

}
