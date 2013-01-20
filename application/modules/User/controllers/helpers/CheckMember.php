<?php

class User_Controller_Helper_CheckMember extends Zend_Controller_Action_Helper_Abstract
{
    public function checkMember($role = "member")
    {
        $session = $this->_actionController->getHelper("currentSession")->direct();

        if (is_null($session->getUser()))
        {
            $session->write("messages", "UNAUTHENTICATED");
            $session->write("messages_class", "error");
            $session->write("return_url", urlencode($this->getRequest()->getRequestUri()));

            $this->_actionController->getHelper("redirector")->gotoRoute(array(), "login", true);
        }
    }

    public function direct()
    {
        $this->checkMember();
    }
}