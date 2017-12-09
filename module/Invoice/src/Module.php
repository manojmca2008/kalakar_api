<?php

namespace Invoice;

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
                Model\UserAccounts::class => function($container) {
                    $tableGateway = $container->get('UserAccountsGateway');
                    return new Model\UserAccounts($tableGateway);
                },
                'UserAccountsGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    return new TableGateway('useraccounts', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\SmsController::class => function($container) {
                    return new Controller\SmsController(
                            $container->get()
                    );
                },
                Controller\NotificationController::class => function($container) {
                    return new Controller\NotificationController(
                            $container->get()
                    );
                },
                Controller\SignupController::class => function($container) {
                    return new Controller\SignupController(
                            $container->get()
                    );
                },
                Controller\SigninController::class => function($container) {
                    return new Controller\SigninController(
                            $container->get()
                    );
                },
                Controller\UserAccountsController::class => function($container) {
                    return new Controller\UserAccountsController(
                            $container->get()
                    );
                },
            ],
        ];
    }

}
