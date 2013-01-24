<?php

class Core_Controller_Helper_ReturnToSession extends Zend_Controller_Action_Helper_Abstract
{
    public function returnToSession($params, $route, $ingore_rest = false)
    {
        $redirector = $this->_actionController->getHelper("Redirector");
        $session = $this->_actionController->getHelper("currentSession")->direct();

        $return_url = $session->read("return_url");
        if (! empty($return_url))
        {
            $session->write("return_url", null);
            return $redirector->gotoUrl(urldecode($return_url));
        }
        else
            return $redirector->gotoRoute($params, $route, $ingore_rest);
    }

    public function direct($params, $route, $ingore_rest = false)
    {
        return $this->returnToSession($params, $route, $ingore_rest);
    }
}