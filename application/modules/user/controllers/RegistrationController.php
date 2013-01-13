<?php

class User_RegistrationController extends Bisna\Controller\Action
{
    public function signupAction()
    {
        if($this->getRequest()->getMethod() === 'POST' && !$this->_getParam("status"))
            return $this->forward('post');

        $this->view->messages = $this->_getParam("messages");
        $this->view->messages_class = $this->_getParam("messages_class");
        $this->view->user = $user = $this->_getParam("user");
    }

    public function postAction()
    {
        $user = $this->_getParam("user");
        try
        {
            $uRepo = $this->em()->getRepository('Entity\User');
            $valid = $uRepo->validate($user);
            $messages = ($valid === true)? array() : $valid;

            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // write uid to session
            $session = new User_Model_Session;
            $session->create();
            $session->set("id", $user->getId());

            $options = array(
                "status" => "success",
                "messages" => "Login OK",
                "messages_class" => "info",
            );
            $this->forward("signup", null, null, $options);
        }
        catch (Zend_Exception $e)
        {
            $options = array(
                "status" => "error",
                "messages" => $messages,
                "messages_class" => "error",
            );
            $this->forward("signup", null, null, $options);
        }
    }

}