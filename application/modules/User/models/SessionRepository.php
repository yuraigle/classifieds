<?php

class User_Model_SessionRepository extends \Doctrine\ORM\EntityRepository
{
    public function expr() {}

    public function validate($user)
    {
        $user = $this->findOneBy(array("email"=>$email, "deleted"=>0));

        $messages = array();
        if (empty($user['email'])) // email set
            $messages[] = "EMAIL_BLANK";
        if (empty($user['password'])) // password set
            $messages[] = "PASSWORD_BLANK";

        return ($messages == array())? true : $messages;
    }

    public function authenticate($email, $password)
    {
        $user = $this->findOneBy(array("email"=>$email, "deleted"=>0));

        $messages = array();
        if (is_null($user))
            $messages[] = "USER_NOT_FOUND";
        elseif (!$this->checkPassword($user, $password))
            $messages[] = "PASSWORD_WRONG";        

        return ($messages == array())? true : $messages;
    }

    public function create()
    {
        Zend_Session::start();
        $session = new Zend_Session_Namespace('Zend_Auth');
        $session->setExpirationSeconds(1209600); // 2 weeks

        $data = array();

        $storage = Zend_Auth::getInstance()->getStorage();
        $storage->write($data);

        Zend_Session::rememberMe(1209600); // 2 weeks
    }
}