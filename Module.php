<?php
namespace UserModule;

use PPI\Framework\Autoload;
use PPI\Framework\Module\AbstractModule;

class Module extends AbstractModule
{
    /**
     * Initialize Module
     */
    public function init($e)
    {
        Autoload::add(__NAMESPACE__, dirname(__DIR__));
    }

    /**
     * Get Module Name
     *
     * @return array
     */
    public function getName()
    {
        return 'UserModule';
    }

    /**
     * Get the Autoloader configuration for this module
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }

    /**
     * Get the configuration for this module
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->loadConfig(__DIR__ . '/resources/config/config.yml');
    }

    /**
     * Get the routes for this module
     *
     */
    public function getRoutes()
    {
        return $this->loadSymfonyRoutes(__DIR__ . '/resources/routes/symfony.yml');
    }

    /**
     * Get the Service Config
     *
     */
    public function getServiceConfig()
    {
        return array('factories' => array(

            'user.storage' => function ($sm) {
                 return new \UserModule\Storage\User($sm->get('datasource')->getConnection('main'));
            },

            'user.security' => function ($sm) {
                $us = new \UserModule\Classes\UserSecurity();
                $us->setSession($sm->get('session'));
                $us->setUserStorage($sm->get('user.storage'));
                $us->setConfig($sm->get('config'));
                return $us;
            },

            'login.helper' => function ($sm) {
                $us = new \UserModule\Classes\UserSecurity();
                $us->setSession($sm->get('session'));
                return $us;
            },

            'user.security.templating.helper' => function ($sm) {
                return new \UserModule\Classes\UserSecurityTemplatingHelper($sm->get('user.security'));
            },

            'user.account.helper' => function ($sm) {
                $helper = new \UserModule\Classes\AccountHelper();
                $helper->setUserStorage($sm->get('user.storage'));
                return $helper;
            }

        ));
    }
}
