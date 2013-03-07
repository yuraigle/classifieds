<?php
putenv("APPLICATION_ENV=testing");

defined('CURRENT_DOMAIN') || define('CURRENT_DOMAIN', 'domain');
defined('APPLICATION_CLI') || define('APPLICATION_CLI', true);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/modules'),
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// composer autoloader
require_once APPLICATION_PATH . '/../vendor/autoload.php';

Zend_Session::$_unitTestEnabled = true;

include_once "BaseTestCase.php";

// doctrine reset (testing env only!) ==========================================
exec("./doctrine orm:schema-tool:drop --force", $out);
echo "Test DB dropped" . PHP_EOL;
exec("./doctrine orm:schema-tool:create", $out);
echo "New test DB schema created" . PHP_EOL;
include_once "populator.php";
// =============================================================================
