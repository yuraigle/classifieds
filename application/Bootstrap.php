<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    // caching
    protected function _initCache()
    {
        $oBackend = new Zend_Cache_Backend_Memcached(
            array(
                'servers' => array( array(
                    'host' => '127.0.0.1',
                    'port' => '11211'
                ) ),
                'compression' => false
            )
        );

        $oFrontend = new Zend_Cache_Core(
            array(
                'caching' => true, // APPLICATION_ENV == 'production',
                'cache_id_prefix' => 'Application_',
                'logging' => false,
                'write_control' => true,
                'automatic_serialization' => true,
                'ignore_user_abort' => true
            )
        );

        $oCache = Zend_Cache::factory( $oFrontend, $oBackend );
        \Zend_Registry::set( "cache", $oCache );
    }

    // translation
    public function _initLocale() {
        \Zend_Registry::set( "start_time", microtime(true) );

        $currentLocale = 'en';
        $locale = new Zend_Locale($currentLocale);
        \Zend_Registry::set('Zend_Locale', $locale);

        $translationFile = APPLICATION_PATH . "/lang/$currentLocale.inc.php";
        $translate = new Zend_Translate('array', $translationFile, $currentLocale);
        \Zend_Registry::set('Zend_Translate', $translate);
    }

    // bootstrap modules
    protected function _initModules()
    {
        $this->bootstrap('frontController');
        $frontController = Zend_Controller_Front::getInstance();
        $modules = $frontController->getControllerDirectory();
        $schema = array(); // doctrine schema paths

        foreach($modules as $module => $path)
        {
            $schema[] = $path . "/../Entity";
            $bootstrapClass = ucfirst($module) . '_Bootstrap';

            Zend_Loader_Autoloader::getInstance()->registerNamespace(ucfirst($module));
            $moduleBootstrap = new $bootstrapClass($this);
            $moduleBootstrap->bootstrap();

            Zend_Controller_Action_HelperBroker::addPath(
                $path . "/helpers",
                ucfirst($module) . "_Controller_Helper_"
            );
        }

        $frontController->registerPlugin(new Core_Plugin_Header());

        \Zend_Registry::set("doctrine_schema", $schema);
        $this->bootstrap('doctrine');
    }

    // debuger in dev mode
    protected function _initZFDebug()
    {
        if(APPLICATION_ENV != 'development')
            return;

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');
        $em = \Zend_Registry::get("em");
        $em->getConnection()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\DebugStack());

        $options = array(
            'plugins' => array(
                'Variables', 'Html', 'Exception', 'Memory', 'Time',
                'ZFDebug_Controller_Plugin_Debug_Plugin_Doctrine2'  => array(
                    'entityManagers' => array($em),
                ),
                'File' => array('basePath' => APPLICATION_PATH),
            )
        );

        $debug = new ZFDebug_Controller_Plugin_Debug($options);

        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin($debug);
    }

    // store session in db
    protected function _initApplicationSession() 
    {
        // do not store sessions for doctrine cli
        if (defined('APPLICATION_CLI') && APPLICATION_CLI == 1) 
            return;

        $this->bootstrap('session');
        $em = \Zend_Registry::get("em");

        Pike_Session_SaveHandler_Doctrine::setEntitityManager($em);
        Zend_Session::start();
    }

    public function _initAppView()
    {
        $view = $this->bootstrap('view')->getResource('view'); 
        $view->headTitle('Site.com');
        $view->headTitle()->setSeparator(' - ');
    }
}
