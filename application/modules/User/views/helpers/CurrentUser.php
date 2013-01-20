<?php

class User_View_Helper_CurrentUser extends Zend_View_Helper_Abstract
{
    public function currentUser()
    {
        $em = \Zend_Registry::get("em");
        $session = $em->find('\User\Entity\Session', Zend_Session::getId());

        return (is_null($session))? null : $session->getUser();
    }
}
