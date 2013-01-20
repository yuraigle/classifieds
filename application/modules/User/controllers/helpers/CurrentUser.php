<?php

class User_Controller_Helper_CurrentUser extends Zend_Controller_Action_Helper_Abstract
{
    public function currentUser()
    {
        $session = $this->_actionController->getHelper("currentSession")->direct();

        return (is_null($session))? null : $session->getUser();
    }

    public function direct()
    {
        return $this->currentUser();
    }
}