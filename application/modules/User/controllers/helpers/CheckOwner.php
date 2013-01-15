<?php

class User_Controller_Helper_CheckOwner extends Zend_Controller_Action_Helper_Abstract
{
    public function checkOwner($uid)
    {
        $user = $this->_actionController->getHelper("currentUser")->direct();

        if ($user->getId() != $uid /* and role != admin|moder? */)
        {
            $this->_actionController->getHelper("messages")->direct("NOT_ALLOWED", "error");
            $this->_actionController->getHelper("redirector")->gotoRoute(array(), "home", true);
        }
    }

    public function direct($uid)
    {
        $this->checkOwner($uid);
    }
}