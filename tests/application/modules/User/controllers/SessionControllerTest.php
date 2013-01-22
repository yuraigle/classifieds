<?php

class User_SessionControllerTest extends BaseTestCase
{
    public function loginUser($user, $password)
    {
        $this->request->setMethod('POST')
                      ->setPost(array('user' => array('email' => $user, 'password' => $password)));

        $this->dispatch('/login');
    }

    public function testFakeSessionStarted()
    {
        $this->assertTrue(strlen(Zend_Session::getId())>0);
    }

    public function testValidLoginRedirectsToHome()
    {
        $this->loginUser('kai@li.ru', 'asdasd');

//        $user = Zend_Controller_Action_HelperBroker::getExistingHelper('currentUser')->direct();
        $this->assertTrue(!is_null($this->currentUser()));
        $this->assertRedirectTo('/');
    }

    public function testInvalidLogin0()
    {
        $this->loginUser('', '');
        $this->assertContains('Email must be specified', $this->getResponse()->getBody());
        $this->assertContains('Password must be specified', $this->getResponse()->getBody());
        $this->assertNotRedirectTo('/');
        $this->assertTrue(is_null($this->currentUser()));
    }

    public function testInvalidLogin1()
    {
        $this->loginUser('kai@li.ru', '');
        $this->assertNotContains('Email must be specified', $this->getResponse()->getBody());
        $this->assertContains('Password must be specified', $this->getResponse()->getBody());
        $this->assertNotRedirectTo('/');
    }

    public function testInvalidLogin2()
    {
        $this->loginUser('', 'qweewq');
        $this->assertContains('Email must be specified', $this->getResponse()->getBody());
        $this->assertNotContains('Password must be specified', $this->getResponse()->getBody());
        $this->assertNotRedirectTo('/');
    }

    public function testLogout()
    {
        $this->dispatch('/logout');
        $this->assertTrue(is_null($this->currentUser()));
    }
}
