<?php

class User_SessionController extends Bisna\Controller\Action
{
    public function loginAction()
    {
        if($this->getRequest()->getMethod() === 'POST' && !$this->_getParam("status"))
            return $this->forward('check');

        $this->view->messages = $this->_getParam("messages");
        $this->view->messages_class = $this->_getParam("messages_class");
        $this->view->user = $user = $this->_getParam("user");
    }

    public function logoutAction()
    {
        // logout
        // ...

        // redirect somewhere
        $this->_helper->redirector->gotoRoute(array(), "login", true);
    }

    public function checkAction()
    {
        $user = $this->_getParam("user");
        try
        {
            $mSession = new User_Model_Session;
            $valid = $mSession->authenticate($user['email'], $user['password']);
            $messages = ($valid === true)? array() : $valid;

            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // write uid to session
            
            $session->create();
            $session->set("id", $user->getId());

            $options = array(
                "status" => "success",
                "messages" => "Login OK",
                "messages_class" => "info",
            );
            $this->forward("login", null, null, $options);
        }
        catch (Zend_Exception $e)
        {
            $options = array(
                "status" => "error",
                "messages" => $messages,
                "messages_class" => "error",
            );
            $this->forward("login", null, null, $options);
        }
    }
}
