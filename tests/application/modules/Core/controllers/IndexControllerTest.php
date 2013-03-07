<?php

class Core_IndexControllerTest extends BaseTestCase
{
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testDoctrineWorks()
    {
        $this->assertTrue(!is_null($this->em()));
    }

    public function testHomePage()
    {
        $this->dispatch('/');

        $this->assertModule('Core');
        $this->assertController('index');
        $this->assertAction('index');
    }
}
