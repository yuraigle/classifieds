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

    public function testCanAddUsers()
    {
        /*
        $faker = Faker\Factory::create();

        for ($i=0; $i<300; $i++)
        {
            $user = new \User\Entity\User();
            $user->setUsername($faker->name);
            $user->setEmail($faker->email);
            $user->setPassword($faker->md5);
            $user->setSalt($faker->md5);
            $user->setPhone($faker->phoneNumber);
            $user->setDescription($faker->text);
            $user->setUrl($faker->url);
            $user->setVerified($faker->boolean);
            $user->setAllowLetters($faker->boolean);
            $user->setCreated($faker->dateTimeBetween('-2 years', 'now'));

            $this->em()->persist($user);
        }

        $this->em()->flush();
        */
    }
}
