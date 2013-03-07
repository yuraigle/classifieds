<?php

class User_SessionControllerTest extends BaseTestCase
{
    public function testFakeSessionStarted()
    {
        $this->assertTrue(strlen(Zend_Session::getId())>0);
    }

    public function testValidLoginRedirectsToHome()
    {
        $this->loginUser(\Populator::$admin_email, \Populator::$admin_password);

        $this->assertTrue(!is_null($this->currentUser()));
        $this->assertRedirectTo('/');
    }

    public function test_Invalid_Login_0()
    {
        $this->loginUser('', '');
        $this->assertContains('Email must be specified', $this->getResponse()->getBody());
        $this->assertNotRedirectTo('/');
        $this->assertTrue(is_null($this->currentUser()));
    }

    public function test_Invalid_Login_1()
    {
        $this->loginUser(\Populator::$admin_email, '');
        $this->assertNotContains('Email must be specified', $this->getResponse()->getBody());
        $this->assertContains('Password must be specified', $this->getResponse()->getBody());
        $this->assertNotRedirectTo('/');
        $this->assertTrue(is_null($this->currentUser()));
    }

    public function test_Invalid_Login_2()
    {
        $this->loginUser('', 'wrong_pass');
        $this->assertContains('Email must be specified', $this->getResponse()->getBody());
        $this->assertNotRedirectTo('/');
        $this->assertTrue(is_null($this->currentUser()));
    }

    public function test_Unauthenticated_Logout()
    {
        $this->dispatch('/logout');
        $this->assertRedirectTo('/');
    }

    public function test_Logout()
    {
        $this->loginUser(\Populator::$admin_email, \Populator::$admin_password);

        $this->resetRequest();
        $this->resetResponse();

        $this->dispatch('/logout');
        $this->assertTrue(is_null($this->currentUser()));
        $this->assertRedirectTo('/');
    }
}
