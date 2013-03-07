<?php

class Admin_Controller_Helper_CheckAdmin extends Zend_Controller_Action_Helper_Abstract
{
    public function checkAdmin()
    {
        // check for logged in
        $this->_actionController->getHelper("checkMember")->direct();

        $session = $this->_actionController->getHelper("currentSession")->direct();
        $user = $session->getUser();

        if (! $user || ! in_array($user->getRole(), array("admin")))
        {
            $session->write("messages", "NOT_ALLOWED");
            $session->write("messages_class", "error");
            $this->_actionController->getHelper("redirector")->gotoRoute(array(), "home", true);
        }
    }

    public function direct()
    {
        $this->checkAdmin();
    }
}
