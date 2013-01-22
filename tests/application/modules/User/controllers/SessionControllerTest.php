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
        $this->assertRedirectTo('/');
    }

    public function testInvalidLogin()
    {
        $this->loginUser('kai@li.ru', '');

        print_r($this->_response);
    }
}
