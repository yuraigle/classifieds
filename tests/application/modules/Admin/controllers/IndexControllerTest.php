<?php

class Admin_IndexControllerTest extends BaseTestCase
{
    public function test_Try_To_Access_Without_Rights()
    {
        $this->dispatch('/admin');

        $this->assertRedirect('/login');
    }

    public function test_Can_Access_With_Rights()
    {
        $this->loginUser(\Populator::$admin_email, \Populator::$admin_password);

        $this->resetRequest();
        $this->resetResponse();

        $this->dispatch('/admin');

        $this->assertModule('Admin');
        $this->assertController('index');
        $this->assertAction('index');
    }
}
