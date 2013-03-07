<?php

/**
 * Base Controller Test Class
 * All controller tests should extend this
 */
abstract class BaseTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    protected $_application;
    protected $backupGlobals = FALSE;

    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->_application = new Zend_Application(APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );

        $front = Zend_Controller_Front::getInstance();
        if($front->getParam('bootstrap') === null)
            $front->setParam('bootstrap', $this->_application->getBootstrap());

        $this->_application->bootstrap();

        $this->createFakeSession();
    }

    public function em()
    {
        return \Zend_Registry::get('em');
    }

    public function tearDown()
    {
        $this->reset();
        $this->clearFakeSession();
    }

    public function currentUser()
    {
        return Zend_Controller_Action_HelperBroker::getExistingHelper('currentUser')->direct();
    }

    /*
     * Fake session in DB
     */
    public function createFakeSession()
    {
        $sessionId = md5("sessionId");
        $session = $this->em()->find('\User\Entity\Session', $sessionId);

        if (is_null($session))
        {
            $session = new \User\Entity\Session();
            $session->setId($sessionId);
            $session->setData("");
        }

        $session->setModified(new \DateTime());

        $this->em()->persist($session);
        $this->em()->flush();

        Zend_Session::setId($sessionId);
    }

    public function clearFakeSession()
    {
        $sessionId = md5("sessionId");
        $session = $this->em()->find('\User\Entity\Session', $sessionId);

        if (is_null($session))
        {
            $session = new \User\Entity\Session();
            $session->setId($sessionId);
        }

        $session->setData("");
        $session->setUser(null);
        $session->setModified(new \DateTime());

        $this->em()->persist($session);
        $this->em()->flush();

        Zend_Session::destroy();
    }

    public function loginUser($user, $password)
    {
        $this->request->setMethod('POST')
            ->setPost(array('user' => array('email' => $user, 'password' => $password)));

        $this->dispatch('/user/session/new');
    }
}