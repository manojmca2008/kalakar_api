<?php

namespace MCommons;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Filter\File\RenameUpload;
use MCommons\Message;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

class StaticFunctions {

    protected static $_serviceLocator;
    protected static $_db_read_adapter;
    protected static $_db_write_adapter;

    public static function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        static::$_serviceLocator = $serviceLocator;
    }

    public static function getServiceLocator() {
        if (!static::$_serviceLocator) {
            throw new \Exception("Unable to get service locator. Please set the instance of service locator first");
        }
        return static::$_serviceLocator;
    }

    public static function setUserAgent($userAgent) {

        if (!empty($userAgent->getFieldValue())) {
            if (preg_match('/Mozilla/i', $userAgent->getFieldValue()) || preg_match('/Chrome/i', $userAgent->getFieldValue()) || preg_match('/Safari/i', $userAgent->getFieldValue())) {
                static:: $_userAgent = "ws";
            } elseif (preg_match('/Android/i', $userAgent->getFieldValue())) {
                static:: $_userAgent = "android";
            } elseif (preg_match('/iOS/i', $userAgent->getFieldValue())) {
                static:: $_userAgent = "iOS";
            } else {
                static:: $_userAgent = "ws";
            }
        } else {
            static:: $_userAgent = "ws";
        }
    }

    public static function getUserAgent() {
        if (!static:: $_userAgent) {
            throw new \Exception("Unable to get user agent. Please set the instance of header for user agent first", 400);
        }
        return static:: $_userAgent;
    }

    public static function setDbReadAdapter(\Zend\Db\Adapter\AdapterServiceFactory $readAdapter) {
        pr($readAdapter, 1);
        static::$_db_read_adapter = $readAdapter;
    }

    public static function getDbReadAdapter() {

        if (!static::$_db_read_adapter) {
            static::setDbReadAdapter(static::getServiceLocator()->get('RestFunction\Db\Adapter\ReadAdapter'));
        }
        return static::$_db_read_adapter;
    }

    public static function setDbWriteAdapter(Adapter $writeAdapter) {
        static::$_db_write_adapter = $writeAdapter;
    }

    public static function getDbWriteAdapter() {
        if (!static::$_db_write_adapter) {
            static::setDbWriteAdapter(static::getServiceLocator()->get('RestFunction\Db\Adapter\WriteAdapter'));
        }
        return static::$_db_write_adapter;
    }

    public static function getFormatter() {
        $sl = static::getServiceLocator();
        $routeMatch = $sl->get('Application')
                ->getMvcEvent()
                ->getRouteMatch();
        $config = $sl->get('config');
        try {
            if (isset($config['api_standards'])) {
                // Get api standards decided
                $apiStandards = $config['api_standards'];

                // Get default formatter text or set it to "formatter"
                $formatterText = isset($apiStandards['formatter_text']) ? $apiStandards['formatter_text'] : "formatter";

                // Set default formatter type from api_standards or set it default to JSON
                $defaultFormatter = isset($apiStandards['default_formatter']) ? $apiStandards['default_formatter'] : "json";

                // Get the formatter from query
                $params = $sl->get('request')
                        ->getQuery()
                        ->getArrayCopy();
                $formatter = isset($params[$formatterText]) ? $params[$formatterText] : $defaultFormatter;
            } else {
                throw new \Exception("Invalid Parameters");
            }
        } catch (\Exception $ex) {
            // On any exception set the formatter to the json
            $formatter = "json";
        }
        return $formatter;
        // $request->get
    }

    /**
     * Generate appropriate response with variables and response code
     *
     * @param ServiceLocatorInterface $sl            
     * @param array $vars            
     * @param number $response_code            
     * @return \Zend\Http\PhpEnvironment\Response $response
     */
    public static function getResponse(ServiceLocatorInterface $sl, array $vars = array(), $response_code = 200) {
        /**
         *
         * @var \Zend\Di\Di $di
         */
        $di = $sl->get('Di');
        /**
         *
         * @var array $configuration
         */
        $configuration = $sl->get('config');
        /**
         *
         * @var PostProcessor\AbstractPostProcessor $postProcessor
         */
        $formatter = StaticFunctions::getFormatter();

        $response = $sl->get('response');

        $postProcessor = $di->get(\Rest\Processors\Json::class, array(
            'vars' => $vars,
            'response' => $response
        ));

        $response->setStatusCode($response_code);
        $postProcessor->process();
        $response = $postProcessor->getResponse();
        return $response;
    }

    public static function getErrorResponse(ServiceLocatorInterface $sl, $message = 'Error Occured', $response_code = 500) {
        /**
         *
         * @var \Zend\Di\Di $di
         */
        $di = $sl->get('di');

        /**
         *
         * @var array $configuration
         */
        $configuration = $sl->get('config');

        /**
         *
         * @var PostProcessor\AbstractPostProcessor $postProcessor
         */
        $formatter = StaticFunctions::getFormatter();
        $response = $sl->get('response');

        $request = $sl->get('request');
        $requestType = (bool) $request->getQuery('mob', false) ? 'mobile' : 'web';

        $vars = StaticFunctions::formatResponse(array(
                    'error' => $message
                        ), $response_code, $message, $requestType);
        $postProcessor = $di->get($formatter . "_processor", array(
            'vars' => $vars,
            'response' => $response
        ));

        $response->setStatusCode($response_code);
        $postProcessor->process();
        $response = $postProcessor->getResponse();
        return $response;
    }

    public static function formatResponse(array $vars = array(), $status_code = 200, $message = 'Success', $device = 'web', $isTokenValid = true) {

        $device = strtolower($device . '');
        $formattedResponse = $vars;
        $config = self::getServiceLocator()->get('config');
        $formattedResponse = array(
            'result' => false,
            'message' => $message,
            'data' => array()
        );
        if ($status_code == 200) {
            $formattedResponse['result'] = (bool) count($vars);
            $formattedResponse['data'] = $vars;
        } else {
            if (!$message) {
                $formattedResponse['message'] = 'An Error Occured';
            }
            $formattedResponse['result'] = (false);
        }
        return $formattedResponse;
    }

    public static function getExpiryHeaders() {
        $headers = new \Zend\Http\Headers();
        $headers->addHeaders(array(
            'Cache-Control' => 'no-cache, must-revalidate',
            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT'
        ));
        return $headers;
    }

    public static function validateImage($file) {
        $mTypes = self::$_allowedImageTypes;
        if (empty($file['type']) || $file['type'] == null) {
            $val_response = array(
                'status' => false,
                'message' => 'File size exceeded, it should be upto ' . MAX_IMAGE_UPLOAD_SIZE_LIMIT . "MB(s)"
            );
        } elseif (!in_array(trim($file['type']), $mTypes)) {
            $val_response = array(
                'status' => false,
                'message' => 'Invalid image'
            );
        } elseif ($file['error'] != 0 && $file['error'] != 4) {
            $err_value = self::getUploadError($file['error']);
            $val_response = array(
                'status' => false,
                'message' => $err_value['msg']
            );
        } elseif (round(($file['size'] / 1048576), 2) > MAX_IMAGE_UPLOAD_SIZE_LIMIT) {
            // size validation
            $val_response = array(
                'status' => false,
                'message' => 'File size exceeded, it should be upto ' . MAX_IMAGE_UPLOAD_SIZE_LIMIT . "MB(s)"
            );
        } else {
            $val_response = array(
                'status' => true,
                'message' => 'Success.'
            );
        }
        return $val_response;
    }

    /**
     * Identify file upload error
     *
     * @param int $error_code            
     * @return array
     */
    public static function getUploadError($error_code) {
        $msg = '';
        $error = '';
        switch ($error_code) {
            case 1:
                $msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                $error = 'UPLOAD_ERR_INI_SIZE';
                break;
            case 2:
                $msg = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the form.';
                $error = 'UPLOAD_ERR_FORM_SIZE';
                break;
            case 3:
                $msg = 'The uploaded file was only partially uploaded.';
                $error = 'UPLOAD_ERR_PARTIAL';
                break;
            case 4:
                $msg = 'No file was uploaded.';
                $error = 'UPLOAD_ERR_NO_FILE';
                break;
            case 6:
                $msg = 'Missing a temporary folder.';
                $error = 'UPLOAD_ERR_NO_TMP_DIR';
                break;
            case 7:
                $msg = 'Failed to write file to disk.';
                $error = 'UPLOAD_ERR_CANT_WRITE';
                break;
            case 8:
                $msg = 'A PHP extension stopped the file upload.';
                $error = 'UPLOAD_ERR_EXTENSION';
                break;
            default:
                $msg = "No message";
                $error = "No error";
                break;

                return array(
                    'msg' => $msg,
                    'error' => $error
                );
        }
    }

    /**
     * function to upload user images
     *
     * @param array $files            
     * @param string $path            
     * @param string $dirname            
     * @throws \Exception
     * @return array
     */
    public static function uploadUserImages($files, $path, $dirname) {
        $isValid = true;
        $response = $resp = [];
        $directories = explode(DS, $dirname);
        $newpath = $path;
        foreach ($directories as $key => $dir) {
            $newpath .= $dir . DS;
            if (!file_exists($newpath)) {
                mkdir($newpath, 0777, true);
            }
        }
        if (!empty($files)) {
            foreach ($files as $fkey => $file) {
                $valid = StaticFunctions::validateImage($file);
                if ($valid['status']) {
                    $filter = new RenameUpload(array(
                        'target' => $path . DIRECTORY_SEPARATOR . $dirname . uniqid(rand(99, 9999) . "-" . mt_rand(11111, 999999) . "-"),
                        'use_upload_extension' => true
                    ));
                    $temp_resp = $filter->filter($files->$fkey);
                    if (isset($temp_resp['tmp_name'])) {
                        $filename = explode(DS, $temp_resp['tmp_name']);
                        $temp_resp['path'] = WEB_URL . $dirname . $filename[count($filename) - 1];
                        unset($temp_resp['tmp_name']);
                        unset($temp_resp['type']);
                        unset($temp_resp['error']);
                        unset($temp_resp['size']);
                    }
                    $resp[$fkey] = $temp_resp;
                } else {
                    $isValid = false;
                }
            }
        }
        if ($isValid) {
            $response = $resp;
        } else {
            throw new \Exception($valid['message'], 400);
        }
        return $response;
    }

    public static function moveFile($file, $path, $newdirname, $olddirname) {
        $fileParts = explode("/", $file);
        $fileName = $fileParts[count($fileParts) - 1];
        $newname = $path . $newdirname . $fileName;
        $directories = explode(DS, $newdirname);
        $newpath = $path;
        foreach ($directories as $key => $dir) {
            $newpath .= $dir . DS;
            if (!file_exists($newpath)) {
                mkdir($newpath, 0777, true);
            }
        }
        try {
            $oldFilePath = $path . $olddirname . $fileName;
            rename($oldFilePath, $newname);
            return WEB_URL . $newdirname . $fileName;
        } catch (\Exception $exp) {
            throw new \Exception($exp->getMessage(), 400);
        }
    }

    public static function sendMail($data) {
        $config = self::getServiceLocator()->get('config');
        $layoutView = new \Zend\View\Model\ViewModel();
        $view = new \Zend\View\Model\ViewModel();
        $layoutView->setTemplate($data['layout']);
        $view->setVariables($data['variables']);
        $view->setTemplate($data['template']);
        try {
            $renderer = self::getServiceLocator()->get('ViewRenderer');
        } catch (\Exception $ex) {
            // It is useful resque email
            $renderer = new \Zend\View\Renderer\PhpRenderer();
            $templateMaps = $config['view_manager']['template_map'];
            $resolver = new \Zend\View\Resolver\TemplateMapResolver($templateMaps);
            $renderer->setResolver($resolver);
        }

        $content = $renderer->render($view);
        $layoutView->setVariables($data['layoutVariables']);
        $content .= $renderer->render($layoutView);
        print_r($content);die;
        if (is_array($data['receiver'])) {
            foreach ($data['receiver'] as $reciever) {
                $mail = new Message();
                $mail->setBody($content);
                $mail->setFrom($data['sender'], $data['senderName']);
                $mail->addTo($reciever);
                $mail->setSubject($data['subject']);
                $mail->Sendmail();
            }
        }
    }

    /**
     * function to fetch data from web service / API
     *
     * @param string $url            
     * @return multitype
     */
    public static function fetchDataFromUrl($url) {
        try {
            $config = array(
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array(
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_AUTOREFERER => true,
                    CURLOPT_HEADER => true
                )
            );
            $client = new \Zend\Http\Client($url, $config);
            $req = $client->getRequest();
            $data = $client->send($req)->getBody();
        } catch (\Exception $ex) {
            return array(
                'error' => $ex->getMessage()
            );
        }
        return $data;
    }

    public static function extract_day_from_date($date) {
        $restaurant_day = array(
            'su',
            'mo',
            'tu',
            'we',
            'th',
            'fr',
            'sa'
        );
        if (empty($date))
            return '';
        $date = strtotime($date);
        $day = date('w', $date);
        return $restaurant_day[$day];
    }

    public static function filterRequestParams($input) {
        $htmlEntities = new \Zend\Filter\HtmlEntities();
        if (is_array($input)) {
            foreach ($input as $ikey => $ival) {
                $input[$ikey] = $htmlEntities->filter($ival);
            }
        } else {
            $input = $htmlEntities->filter($input);
        }
        return $input;
    }

    public static function pubnubPushNotification($message) {
        $user_current_notification = [];
        $uNotId = 0;
        if (isset($message['channel']) && $message['channel'] != '') {
            $exId = explode('_', $message['channel']);
            if (isset($exId[1]) && is_numeric($exId[1])) {
                $uNotId = $exId[1];
            } else if (isset($exId[2]) && is_numeric($exId[2])) {
                $uNotId = $exId[2];
            } else {
                $uNotId = 0;
            }
        }
        if (!self::getPermissionToSendNotification($uNotId) && ($exId[0] != "dashboard" || $exId[0] != "cmsdashboard")) {
            return true;
        }

        $type = [
            "myorders" => 2,
            "bill" => 18,
            "order" => 1,
            "reservation" => 3,
            "cancelreservation" => 15,
            "reviews" => 4,
            "others" => 0,
            "invite_friends" => 5,
            //"myfriends"=>5,
            "myreviews" => 6,
            "tip" => 7,
            "upload_photo" => 8,
            "bookmark" => 9,
            "friendship" => 10,
            "checkin" => 11,
            "snag-a-spot" => 19//, as discussed with Parmanad sir, close feed type from now.
                //"feed"=>12
        ];
        self::pubnub($message['channel'], $message);
        if (isset($message['msg'])) {
            $notificationModel = self::getServiceLocator()->get(\User\Model\UserNotification::class);
            $str_time = $notificationModel->getDayDifference($message['curDate'], $message['curDate']); //pr($message['curDate'],1);die('test');
            $notification = $notificationModel->countUserNotification($message['userId']);
            if (isset($message['sendcountzero']) && $message['sendcountzero'] == 0) {
                $count = 0;
            } else {
                $count = ($notification[0]['notifications'] == 0) ? 1 : $notification[0]['notifications'];
            }
            foreach ($message as $key => $val) {
                if (array_key_exists($message['type'], $type)) {
                    $user_current_notification['type'] = $type[$message['type']];
                }
                if ($key == 'userId') {
                    $user_current_notification['user_id'] = $val;
                } else if ($key == 'restaurantId') {
                    $user_current_notification['restaurant_id'] = $val;
                } else {
                    $user_current_notification[$key] = $val;
                }
            }
            $user_current_notification['msg_time'] = $str_time;
            self::pubnub('ios_' . $message['channel'], $message['msg']);
            self::pubnub('android_' . $message['channel'], $message['msg']);
        }
    }

    public static function pubnub($channel, $message) {
        $pnconf = new \PubNub\PNConfiguration();
        $config = self::getServiceLocator()->get('config');
        $pnconf->setSubscribeKey($config['constants']['pubnub']['PUBNUB_SUBSCRIBE_KEY']);
        $pnconf->setPublishKey($config['constants']['pubnub']['PUBNUB_PUBLISH_KEY']);
        $pnconf->setSecure(false);
        $pubnub = new \PubNub\PubNub($pnconf);
        // Subscribe is not async and will block the execution until complete.
        $result = $pubnub->publish()->channel($channel)->message($message)->sync();
    }

    /**
     * Extract image from base64 string and save it
     *
     * @param string $string            
     * @param string $basePath
     *            (base path upto public directory)
     * @param string $destinationDir
     *            (destination under public directory can be split with / if multiple subdirectories exists)
     * @throws \Exception
     * @return string
     */
    public static function getImagePath($base64_string = "", $basePath = "", $destinationDir = "") {
        if ($base64_string == "") {
            throw new \Exception("Image data dose not exits", 400);
        }
        // fetches image mime type from base64 string
        $pos = strpos($base64_string, ';');
        $type = explode(':', substr($base64_string, 0, $pos));
        $extension = array_search($type[1], self::$_allowedImageTypes);
        // check if image is valid
        if ($extension == "") {
            throw new \Exception("Invalid image.", 400);
        }

        // creates dir if dosenot exists
        if ($destinationDir != "") {
            $directories = explode(DS, $destinationDir);
            $newpath = $basePath;
            foreach ($directories as $key => $dir) {
                if ($dir != '') {
                    $newpath .= $dir . DS;
                }
                if (!file_exists($newpath)) {
                    mkdir($newpath, 0777, true);
                }
            }
        }

        // get userId from session
        $session = self::getUserSession();
        $userId = $session->getUserId();

        // fetches image mime type from base64 string
        $pos = strpos($base64_string, ';');
        $type = explode(':', substr($base64_string, 0, $pos));
        $extension = array_search($type[1], self::$_allowedImageTypes);
        // check if image is valid
        if ($extension == "") {
            throw new \Exception("Invalid image.", 400);
        }
        // fetches actual image data
        $base64_new_string = explode(",", $base64_string);
        $base64_string = $base64_new_string[1];
        // if base path dosenot exists, it uses temp system directory path
        if ($basePath == "") {
            $outputPath = ini_get('upload_tmp_dir');
            if (!$outputPath || $outputPath == "") {
                $outputPath = sys_get_temp_dir();
            }
            if (!$outputPath || $outputPath == "") {
                throw new \Exception("Invalid Temporary Path to Upload Files");
            }
        } else {
            // uses user defained path
            $outputPath = $basePath . $destinationDir;
            $returnPath = WEB_URL . $destinationDir;
        }
        $uniqueId = uniqid();
        $output_file = $outputPath . DIRECTORY_SEPARATOR . $uniqueId . "_" . $userId . "." . $extension;
        $return_file = $returnPath . $uniqueId . "_" . $userId . "." . $extension;
        // open file
        $ifp = @fopen($output_file, "wb");
        if (!$ifp) {
            throw new \Exception("Cannot open file for writing data");
        }
        // write file
        fwrite($ifp, base64_decode($base64_string));
        // close file
        fclose($ifp);
        return ($return_file);
    }

    public static function resquePush($data = array(), $class = "") {
        //print_r($data);exit;
        $config = self::getServiceLocator()->get('config');
        if (empty($class) || $class == "SendEmail") {

            if (isset($config['resque-service']) && $config['resque-service']) {
                $token = \Resque::enqueue($config['constants']['redis']['channel'], 'SendEmail', $data, true);
                $status = new \Resque_Job_Status($token);
                return $status->get(); // Outputs the status
            } else {
                // sends email manually, if resque is disabled
                StaticFunctions::sendMail($data['sender'], $data['sendername'], $data['receivers'], $data['template'], $data['layout'], $data['variables'], $data['subject']);
            }
        } elseif ($class == "UploadS3") {
            if (isset($config['resque-service']) && $config['resque-service']) {
                $token = \Resque::enqueue($config['constants']['redis']['channel'], 'UploadS3', $data, true);
                $status = new \Resque_Job_Status($token);
                return $status->get(); // Outputs the status
            } else {
                // if resque is disabled it will helps to upload manually
                self::s3UploadImage($data);
            }
        } elseif ($class == "clevertap") {
            if (isset($config['clevertap_service']) && $config['clevertap_service']) {
                $token = \Resque::enqueue("netcore", 'netcore', $data, true);
                $status = new \Resque_Job_Status($token);
                return $status->get(); // Outputs the status
            }
        } elseif ($class == "activityLog") {
            if (isset($config['activity-log']) && $config['activity-log']) {
                $token = \Resque::enqueue($config['constants']['activityLogRedis']['channel'], 'ActivityLog', $data, true);
                $status = new \Resque_Job_Status($token);
                return $status->get(); // Outputs the status
            }
        } else {
            throw new \Exception($class . "Resque class dose not exists.", 400);
        }
    }

    private static function s3UploadImage($args) {
        $config = self::getServiceLocator()->get('config');
        $users = new \User\Model\User();
        $s3 = new S3Lib();
        $image_url = $config['image_base_urls']['local-api'];
        $bucket_name = $config['s3']['bucket_name'];
        $images = array();
        if ($args['op_type'] == 'all') {
            $images = $users->getDpImagesForUpload();
        } else
        if ($args['op_type'] == 'one') {
            $images = $users->getDpImagesForUpload($args['user_id']);
        }
        if (!empty($images)) {
            foreach ($images as $image) {
                $status = false;
                if (isset($image['display_pic_url']) && !empty($image['display_pic_url']) && $image['display_pic_url'] != null) {
                    $original_image = $image_url . "/user_images/profile/" . $image['id'] . "/" . $image['display_pic_url'];
                    $result = $s3->createObject($bucket_name, "/user_images/profile/" . $image['id'] . "/" . $image['display_pic_url'], $original_image);
                    if ($result) {
                        $status = true;
                    }
                }
                if (isset($image['display_pic_url_normal']) && !empty($image['display_pic_url_normal']) && $image['display_pic_url_normal'] != null) {
                    $normal_image = $image_url . "/user_images/profile/" . $image['id'] . "/" . $image['display_pic_url_normal'];
                    $result = $s3->createObject($bucket_name, "/user_images/profile/" . $image['id'] . "/" . $image['display_pic_url_normal'], $original_image);
                    if ($result) {
                        $status = true;
                    }
                }
                if (isset($image['display_pic_url_large']) && !empty($image['display_pic_url_large']) && $image['display_pic_url_large'] != null) {
                    $large_image = $image_url . "/user_images/profile/" . $image['id'] . "/" . $image['display_pic_url_large'];
                    $result = $s3->createObject($bucket_name, "/user_images/profile/" . $image['id'] . "/" . $image['display_pic_url_large'], $original_image);
                    if ($result) {
                        $status = true;
                    }
                }
                if ($status) {
                    try {
                        $users->updateImageStatus($image['id']);
                    } catch (\Exception $ex) {
                        
                    }
                }
            }
            return true;
        }
    }

    /**
     * 
     * @return Zend\Cache\Storage\Adapter\Redis
     */
    public static function getRedisCache() {
        $sl = StaticFunctions::getServiceLocator();
        $redis_cache = $sl->get("RedisCache");
        return $redis_cache;
    }

    public static function uploadImageBase64($base64_string_array = array(), $basePath = "", $destinationDir = "") {
        $return_file = array();
        if (empty($base64_string_array)) {
            return $return_file;
        }
        // creates dir if dosenot exists
        if ($destinationDir != "") {
            $directories = explode(DS, $destinationDir);
            $newpath = $basePath;
            foreach ($directories as $key => $dir) {
                if ($dir != '') {
                    $newpath .= $dir . DS;
                }
                if (!file_exists($newpath)) {
                    mkdir($newpath, 0777, true);
                }
            }
        }

        // get userId from session
        $session = self::getUserSession();
        $userId = $session->getUserId();

        // fetches image mime type from base64 string
        foreach ($base64_string_array as $key => $base64_string) {
            $pos = strpos($base64_string, ';');
            $type = explode(':', substr($base64_string, 0, $pos));
            $extension = array_search($type[1], self::$_allowedImageTypes);
            // check if image is valid
            if ($extension == "") {
                return $return_file;
            }
            // fetches actual image data
            $base64_new_string = explode(",", $base64_string);
            $base64_string = $base64_new_string[1];

            $outputPath = $basePath . $destinationDir;
            $returnPath = WEB_URL . $destinationDir;
            $filename = uniqid(rand(99, 9999)) . "-" . mt_rand(11111, 999999) . "." . $extension;
            $output_file = $outputPath . DIRECTORY_SEPARATOR . $filename;
            $return_file[$key] = $returnPath . $filename;
            // open file
            $ifp = @fopen($output_file, "wb");
            if (!$ifp) {
                throw new \Exception("Cannot open file for writing data");
            }
            // write file
            fwrite($ifp, base64_decode($base64_string));
            // close file
            fclose($ifp);
        }
        return $return_file;
    }

    public static function sendSmsClickaTell($userSmsData, $userId = 0) {
        $sl = static::getServiceLocator();
        $config = $sl->get('config');
        $clickatellConfig = $config['clikcatell'];
        $userMobNo = preg_replace('/\s+/', '', $clickatellConfig['country_code_us'] . $userSmsData['user_mob_no']);
        $SmsText = $userSmsData['message'];
        $clickatell = new \ClickaTell();
        $data = $clickatell->sendSms($clickatellConfig, $userMobNo, $SmsText);
        return $data;
    }

    public static function resquePDF($data = array(), $class = "") {
        $config = self::getServiceLocator()->get('config');
        if (empty($class) || $class == "SendEmail") {
            return StaticFunctions::sendMailPDF($data['sender'], $data['sendername'], $data['receivers'], $data['template'], $data['layout'], $data['variables'], $data['subject']);
        } else {
            return '';
        }
    }

    public static function sendMailPDF($sender, $sendername, $recievers, $template, $layout, $variables, $subject) {
        $config = self::getServiceLocator()->get('config');
        $layoutView = new ViewModel();
        $view = new ViewModel();
        $layoutView->setTemplate($layout);
        $variables['hostname'] = PROTOCOL . $config['constants']['web_url'];
        $view->setVariables($variables);
        $view->setTemplate($template);
        try {
            $renderer = self::getServiceLocator()->get('ViewRenderer');
        } catch (\Exception $ex) {
            $renderer = new PhpRenderer();
            $templateMaps = $config['view_manager']['template_map'];
            $resolver = new \Zend\View\Resolver\TemplateMapResolver($templateMaps);
            $renderer->setResolver($resolver);
        }
        $content = $renderer->render($view);
        $layoutVariables = array(
            'content' => $content,
            'web_url' => PROTOCOL . $config['constants']['web_url'],
            'order_url' => PROTOCOL . $config['constants']['web_url'] . "/order",
            'reserve_url' => PROTOCOL . $config['constants']['web_url'] . "/reserve",
            'privacy_url' => PROTOCOL . $config['constants']['web_url'] . "/privacy",
            'terms_url' => PROTOCOL . $config['constants']['web_url'] . "/terms",
            'support_url' => PROTOCOL . $config['constants']['web_url'] . "/support"
        );
        $layoutView->setVariables($layoutVariables);
        $content = $renderer->render($layoutView);
        return $content;
    }

}
