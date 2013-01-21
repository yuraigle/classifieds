<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/modules'),
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../../vendor'),
    realpath(APPLICATION_PATH . '/../../doctrine'),
    get_include_path(),
)));

// composer autoloader
require APPLICATION_PATH . '/../vendor/autoload.php';

require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance();


/**
* Base Controller Test Class
*
* All controller tests should extend this
*/
abstract class BaseTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

        parent::setUp();
    }

    public function em()
    {
        return \Zend_Registry::get('em');
    }

    public function tearDown() {}
}
