<?php

namespace MCommons;

class FormProcessor {

    protected $pubnub;

    public function __construct() {
        
    }

    public function processFormData($data) {
        $errors = [];
        $formData = [];
        if (!empty($data)) {
            foreach ($data as $field => $data) {
                $replace = ['required', '-'];
                $colums = str_replace($replace, '', $field);
                $field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", " ", $field));
                $formData[$colums] = $data;
                if ((strstr($field, 'required')) && (empty($data))) {
                    $field = ucwords(str_replace('required', '', $field));
                    $errors[] = "A required field was left blank: $field ";
                    throw new \Exception("A required field was left blank: $field ", 400);
                }
                if ((strstr($field, 'mobile')) && (!empty($data)) && (!preg_match('/^[0-9]{10}+$/', $data))) {
                    $errors[] = "Mobile Number should be 10 digits: $data";
                    throw new \Exception("Mobile Number should be 10 digits: $data", 400);
                }
                if ((strstr($field, 'email')) && (!empty($data)) && (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $data))) {
                    $errors[] = "This email address is not valid: $data";
                    throw new \Exception("This email address is not valid: $data", 400);
                }
                // Error checking on select boxes when the defailt is "didnotchoose" (see README)
                if ((is_array($data)) && (in_array('didnotchoose', $data)) && count($data) == 1) {
                    $field = ucwords(str_replace('required', '', $field));
                    $errors[] = "A required field was left blank: $field";
                    throw new \Exception("A required field was left blank: $field", 400);
                }
            }
        }
    }
}
