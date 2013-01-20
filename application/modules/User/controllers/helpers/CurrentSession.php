<?php

class User_Controller_Helper_CurrentSession extends Zend_Controller_Action_Helper_Abstract
{
    public function currentSession()
    {
        $em = \Zend_Registry::get("em");

        return $em->find('\User\Entity\Session', Zend_Session::getId());
    }

    public function direct()
    {
        return $this->currentSession();
    }
}