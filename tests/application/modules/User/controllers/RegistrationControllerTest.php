<?php

class User_RegistrationControllerTest extends BaseTestCase
{
    public function test_Signup_Page_Exists()
    {
        $this->dispatch('/signup');

        $this->assertModule('User');
        $this->assertController('registration');
        $this->assertAction('new');
    }

    public function test_Invalid_Signup_0()
    {
        $this->request->setMethod('POST')
            ->setPost(array('user' => array('username' => '','email' => '', 'password' => '', 'captcha' => array('id'=>'','input'=>''))));
        $this->dispatch('/signup');

        $this->assertContains('Username must be specified', $this->getResponse()->getBody());
        $this->assertContains('Email must be specified', $this->getResponse()->getBody());
        $this->assertContains('Password must be specified', $this->getResponse()->getBody());
        $this->assertContains('Captcha value is wrong', $this->getResponse()->getBody());
    }
}
