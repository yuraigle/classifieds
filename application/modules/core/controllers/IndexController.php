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
            "deleted" => false,
        );

        /*$user = new \Entity\User();
        $user->setUsername("Rene");
        $user->setEmail("rene@gmail.com");
        $user->setSalt(md5(uniqid(rand(), TRUE)));
        $user->setPassword(md5("qwerty" . $user->getSalt()));
        $user->setDeleted(false);

        $this->em()->persist($user);
        $this->em()->flush();*/

        $user = $this->em()->find('\Entity\User', 6);
        $arr = $user->getArrayCopy();

        print_r($arr);
    }
}
