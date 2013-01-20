<?php

class User_Controller_Helper_CheckOwner extends Zend_Controller_Action_Helper_Abstract
{
    public function checkOwner($uid)
    {
        $session = $this->_actionController->getHelper("currentSession")->direct();
        $user = $session->getUser();

        if ($user->getId() != $uid && ! in_array($user->getRole(), array("admin", "moderator")))
        {
            $session->write("messages", "NOT_ALLOWED");
            $session->write("messages_class", "error");
            $this->_actionController->getHelper("redirector")->gotoRoute(array(), "home", true);
        }
    }

    public function direct($uid)
    {
        $this->checkOwner($uid);
    }
}