<?php

class Admin_UserController extends Admin_Model_Controller
{
    // users list
    public function indexAction()
    {
        $dql = "SELECT u from \User\Entity\User u WHERE u.deleted=0";

        $query = $this->em()->createQuery($dql);
        $adapter = new \Core_Model_PaginatorAdapter($query);
        $this->view->paginator = $paginator = new \Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam("page", 1));
    }

    // show profile edit form in modal window
    public function editAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');

        $user = $this->em()->find("\User\Entity\User", $this->_getParam("id"));
        if (is_null($user)) {throw new Zend_Exception("ERROR", 404);}

        $this->view->user = $user->getArrayCopy();
    }

    // populate fake data
    public function populateAction()
    {
        // clear table. assuming user#1 is admin
        $dql = 'delete from \User\Entity\User u where u.id > 1';
        $this->em()->createQuery($dql)->execute();

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

        $session = $this->_helper->currentSession();
        $session->write("messages", "Fake data created");
        $session->write("messages_class", "info");

        $this->_helper->redirector->gotoRoute(array("controller"=>"user"), "admin", true);
    }
}
