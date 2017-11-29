<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
function pr($obj, $exit = false) {
    if(APPLICATION_ENV != 'local'){
        return ;
    }
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    echo "<pre>";
    echo "\n===== Called from " . $caller['file'] . " " . $caller['line'] . " =====\n\n";
    print_r($obj);
    echo "\n\n";
    if ($exit) {
        exit;
    }
}

/**
 * Works only in local environment. No effect in other environments.
 * @param mixed $obj object to be var_dump(ed]
 * @param bool $exit defaults to false. If true exits the app.
 * @return NULL
 */
function vd($obj, $exit = false) {
    if(APPLICATION_ENV != 'local'){
        return ;
    }
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    echo "<pre>";
    echo "\n===== Called from " . $caller['file'] . " " . $caller['line'] . " =====\n\n";
    var_dump($obj);
    echo "\n\n";
    if ($exit) {
        exit;
    }
}

return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn'    => 'mysql:host=localhost;dbname=kalakar',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'errors' => [
        'show_exceptions' => [
            'message' => true,
            'trace' => true
       ]
    ],
    'di' => [
        'instance' => [
            'alias' => [
                'json_processor' => 'Rest\Processors\Json',
                'image_processor' => 'Rest\Processors\Image',
                'xml_processor' => 'Rest\Processors\Xml',
                'phps_processor' => 'Rest\Processors\Phps'
            ]
        ]
    ],
    'resque-service' => 1,//1 - enabled, 0 - disabled
);