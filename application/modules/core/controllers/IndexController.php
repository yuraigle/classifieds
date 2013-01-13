<?php

class Core_IndexController extends Bisna\Controller\Action
{
    public function init()
    {
        // Initialize action controller here
    }

    public function indexAction()
    {
        // action body
        $request = array(
            "username" => "Rene",
            "password" => "qwerty",
            "email" => "rene@gmail.com",
        );

        $user = new \Entity\User();
        // $user->populate($request);
        $user->setUsername("Rene");
        $user->setPassword("qwerty");
        $user->setEmail("rene@gmail.com");

        $this->em()->persist($user);
        $this->em()->flush();
    }
}
