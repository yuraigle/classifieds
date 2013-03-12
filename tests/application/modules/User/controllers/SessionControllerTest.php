<?php

class User_SessionControllerTest extends BaseTestCase
{
    public function test_Fake_Session_Started()
    {
        $this->assertTrue(strlen(Zend_Session::getId())>0);
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

    public function test_Admin_Login_Works()
    {
        $this->loginUser(\Populator::$admin_email, \Populator::$admin_password);

        $this->assertTrue(!is_null($this->currentUser()));
        $this->assertRedirectTo('/');
    }

    public function test_Any_Member_Login_Works()
    {
        $faker = Faker\Factory::create();

        $members = $this->em()->getRepository('\User\Entity\User')
            ->findBy(array("role" => "member"));
        $user = $faker->randomElement($members);

        $this->loginUser($user->getEmail(), \Populator::$member_password);

        $this->assertTrue(!is_null($this->currentUser()));
        $this->assertRedirectTo('/');
    }
}
