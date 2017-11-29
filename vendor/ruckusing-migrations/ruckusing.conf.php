<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

$dbConfig = file_get_contents(dirname(__FILE__) . '/../../config/autoload/masters.db.php');

if (!empty($dbConfig)) {
    $configArr = explode(',', $dbConfig);
    $userConfig = $configArr[3];
    $userConfig = explode('=>', $userConfig);
    $username = $userConfig[1];
    
    $userConfig = $configArr[4];
    $userConfig = explode('=>', $userConfig);
    $password = $userConfig[1];
    
    $hostConfig = explode('=>', $configArr[5]);
   
    $hostConfig = explode('=>', $hostConfig[1]);
    
    $hostConfig = explode(';', $hostConfig[0]);
    
    $hostDetail = explode('=', $hostConfig[0]);
    $dbDetail = explode('=', $hostConfig[1]);
    
    $hostname = $hostDetail[1];
    $dbname = $dbDetail[1];
}

return array(
    'db' => array(
        APPLICATION_ENV => array(
            'type' => 'mysql',
            'host' => str_replace("'", "", trim($hostname)),
            'port' => 3306,
            'database' => str_replace("'", "", trim($dbname)),
            'user' => str_replace("'", "", trim($username)),
            'password' => str_replace("'", "", trim($password)),
        //'directory' => 'custom_name',
        //'socket' => '/var/run/mysqld/mysqld.sock'
        ),
    ),
    'migrations_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'migrations',
    'db_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'db',
    'log_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'logs',
    'ruckusing_base' => dirname(__FILE__) . DIRECTORY_SEPARATOR
);
