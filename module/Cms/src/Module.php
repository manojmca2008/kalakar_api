<?php

namespace Cms;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Application\Util\Translator;
use Zend\Validator\AbstractValidator;
use Zend\I18n\Translator\Resources;

class Module implements ConfigProviderInterface {

    public function onBootstrap(MvcEvent $e) {
        $translator = Translator::factory(['locale' => 'pt_BR',]);
        $translator->addTranslationFilePattern(
                'phparray', // WARNING, NO UPPERCASE
                Resources::getBasePath(), Resources::getPatternForValidator()
        );
        AbstractValidator::setDefaultTranslator($translator);
    }
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                Model\User::class => function($container) {
                    $tableGateway = $container->get('UserGateway');
                    return new Model\User($tableGateway);
                },
                'UserGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
                Model\State::class => function($container) {
                    $tableGateway = $container->get('StateGateway');
                    return new Model\State($tableGateway);
                },
                'StateGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    return new TableGateway('states', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\UserController::class => function($container) {
                    return new Controller\UserController(
                            $container->get()
                    );
                },
                Controller\LoginController::class => function($container) {
                    return new Controller\LoginController(
                            $container->get()
                    );
                },
                Controller\LogoutController::class => function($container) {
                    return new Controller\LogoutController(
                            $container->get()
                    );
                },
                Controller\RegisterController::class => function($container) {
                    return new Controller\RegisterController(
                            $container->get()
                    );
                },
                Controller\TestController::class => function($container) {
                    return new Controller\TestController(
                            $container->get()
                    );
                },
            ],
        ];
    }
}
