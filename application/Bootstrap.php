<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    // autoloader for doctrine2
    public function _initDoctrineContainer()
    {
        $this->bootstrap('Doctrine'); // Create EntityManager
        $em = $this->getResource('doctrine')->getEntityManager();

        $em->getEventManager()->addEventSubscriber(new \Gedmo\Timestampable\TimestampableListener());
        $em->getEventManager()->addEventSubscriber(new \Gedmo\Sluggable\SluggableListener());

        Zend_Registry::set("em", $em);
    }

    // translation
    public function _initLocale() {
        $currentLocale = 'en';
        $locale = new Zend_Locale($currentLocale);
        Zend_Registry::set('Zend_Locale', $locale);

        $translationFile = APPLICATION_PATH . "/lang/$currentLocale.inc.php";
        $translate = new Zend_Translate('array', $translationFile, $currentLocale);
        Zend_Registry::set('Zend_Translate', $translate);
    }

    protected function _initFrontController()
    {
        $frontController = Zend_Controller_Front::getInstance()
            ->setDefaultModule("Core")
            ->addModuleDirectory(APPLICATION_PATH . "/modules/")
            ->setParam('prefixDefaultModule', 'true');

        return $frontController;
    }

    protected function _initModules()
    {
        $frontController = $this->getResource("FrontController");
        $modules = $frontController->getControllerDirectory();
        foreach($modules as $module => $path)
        {
            $path_bs = $path . "/../Bootstrap.php";
            $bootstrapClass = ucfirst($module) . '_Bootstrap';

            include_once $path_bs;
            $moduleBootstrap = new $bootstrapClass($this);
            $moduleBootstrap->bootstrap();

            Zend_Controller_Action_HelperBroker::addPath(
                $path . "/helpers",
                ucfirst($module) . "_Controller_Helper_"
            );

            Zend_Loader_Autoloader::getInstance()->registerNamespace(ucfirst($module));
        }
    }

    protected function _initApplicationSession() 
    {
        // do not store sessions for doctrine cli
        if (defined('APPLICATION_CLI') && APPLICATION_CLI == 1) {return;}

        $this->bootstrap('doctrine');
        $this->bootstrap('session');
        $em = $this->getResource('Doctrine')->getEntityManager();
    
        Pike_Session_SaveHandler_Doctrine::setEntitityManager($em);
        Zend_Session::start();
    }
}
