<?php

class User_SessionControllerTest extends BaseTestCase
{
    public function loginUser($user, $password)
    {
        $this->request->setMethod('POST')
                      ->setPost(array("user" => array(
                            'email' => $user,
                            'password' => $password,
                        )));
        $this->dispatch('/login');

        $this->assertRedirectTo('/');
 
        $this->resetRequest()
             ->resetResponse();
 
        $this->request->setPost(array());
    }

    public function testSessionStarted()
    {
        $this->assertTrue(Zend_Session::getId());
    }

    public function testValidLogin()
    {
//        $this->loginUser('kai@li.ru', 'asdasd');
    }
}
