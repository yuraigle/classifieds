<?php

class Admin_IndexControllerTest extends BaseTestCase
{
    public function test_Try_To_Access_Without_Rights()
    {
        $this->dispatch('/admin');

        $this->assertRedirect('/login');
    }

}
