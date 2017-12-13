<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Zend\Mvc\MvcEvent;

$constants = isset($constants) ? $constants : function () {
    
};
if (isset($e)) {
    $e = $e ? $e : new MvcEvent();
    $request = $e->getTarget()->getServiceLocator   ()->get('Request');
    if (!defined('PROTOCOL')) {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            define('PROTOCOL', sprintf('%s://', $_SERVER['HTTP_X_FORWARDED_PROTO']));
        } else {
            define('PROTOCOL', sprintf('%s://', $request->getUri()->getScheme()));
        }
    }
    if (!defined('WEB_HOST')) {
        define('WEB_HOST', sprintf(PROTOCOL . $request->getUri()->getHost()));
    }
    define('SITE_URL', $constants('web_url') . '/');
} else if (!defined('PROTOCOL') || !defined('WEB_HOST')) {
    defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'local'));
    define('DS', "/");
    define('BASE_DIR', realpath(__DIR__ . "/../"));
    if (APPLICATION_ENV == 'qc' || APPLICATION_ENV == 'qa') {
        define('PROTOCOL', 'http://');
        define('WEB_HOST', PROTOCOL . 'api.kalakar.com');
        define('SITE_URL', "qc.kalakar.in" . '/');
    } else if (APPLICATION_ENV == 'local') {
        define('PROTOCOL', 'http://');
        define('WEB_HOST', PROTOCOL . 'api.kalakar.com');
        define('SITE_URL', "http://localhost:3000" . '/');
    } else if (APPLICATION_ENV == 'demo') {
        define('PROTOCOL', 'https://');
        define('WEB_HOST', PROTOCOL . 'demoapi.kalakar.com');
        define('SITE_URL', "demo.kalakar.com" . '/');
    } else {
        define('PROTOCOL', 'https://');
        define('WEB_HOST', PROTOCOL . 'api.kalakar.com');
        define('SITE_URL', "kalakar.com" . '/');
    }
}
define('WEB_URL', WEB_HOST . '/');
define('WEB_HOST_URL', sprintf(PROTOCOL . SITE_URL));
define('NO_IMAGE_PATH', WEB_URL . 'img/');
define('TEMPLATE_IMG_PATH', WEB_URL . 'img/');
