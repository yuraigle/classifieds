<?php

class User_Controller_Helper_CheckMember extends Zend_Controller_Action_Helper_Abstract
{
    public function checkMember($role = "member")
    {
        $user = $this->_actionController->getHelper("currentUser")->direct();

        if (is_null($user))
        {
            $this->_actionController->getHelper("messages")->direct("UNAUTHENTICATED", "error");
            // TODO: store current url in session
            $this->_actionController->getHelper("redirector")->gotoRoute(array(), "login", true);
        }
    }

    public function direct()
    {
        $this->checkMember();
    }
}