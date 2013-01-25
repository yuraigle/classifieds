<?php

class Admin_IndexController extends Admin_Model_Controller
{
    public function indexAction()
    {
        // fake data
        /*
        $faker = Faker\Factory::create();

        for ($i=0; $i<5000; $i++)
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
