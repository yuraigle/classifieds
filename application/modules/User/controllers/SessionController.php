<?php

class User_SessionController extends Bisna\Controller\Action
{
    public function loginAction()
    {
        $session = $this->_helper->currentSession();

        // redirect if already logged in
        if ($this->_helper->currentUser())
        {
            $session->write("messages", "ALREADY_LOGGED_IN");
            $session->write("messages_class", null);

            $this->_helper->redirector->gotoRoute(array(), "home", true);
        }
/*
        if($this->getRequest()->getMethod() === 'POST')
        {
            try
            {
                $request = $this->_getParam("user");

                // validate form
                $valid = $this->em()->getRepository('\User\Entity\Session')
                    ->validate($request);

                $messages = ($valid===true)? array():$valid;
                if (! empty($messages))
                    throw new Zend_Exception("Validation errors");

                // authenticate
                $user = $this->em()->getRepository('\User\Entity\User')
                    ->authenticate($request['email'], $request['password']);

                $messages = (is_a($user, '\User\Entity\User'))? array() : $user;
                if (! empty($messages))
                    throw new Zend_Exception("Validation errors");

                // write in session
                $session->setUser($user);
                $this->em()->persist($session);
                $this->em()->flush();

                // messages in session
                $session->write("messages", "LOGGED_IN_OK");
                $session->write("messages_class", "success");

                // get redirect link from session
                $return_url = $session->read("return_url");
                if (! empty($return_url))
                {
                    $session->write("return_url", null);
                    return $this->redirect(urldecode($return_url));
                }
                else
                    return $this->_helper->redirector->gotoRoute(array(), "home", true);
            }
            catch (Zend_Exception $e)
            {
                $this->view->messages = $messages;
                $this->view->messages_class = "error";
            }
        }*/

        $this->view->user = $user = $this->_getParam("user");
    }

    public function newAction()
    {
        $session = $this->_helper->currentSession();

        // redirect if already logged in
        if ($this->_helper->currentUser())
        {
            $session->write("messages", "ALREADY_LOGGED_IN");
            $session->write("messages_class", null);

            $this->_helper->redirector->gotoRoute(array(), "home", true);
        }

        if($this->getRequest()->getMethod() === 'POST')
        {
            $this->forward("create");
        }

        $this->view->user = $user = $this->_getParam("user");
    }

    public function createAction()
    {
        $session = $this->_helper->currentSession();

        $request = $this->getParam("user");
        $adapter = new \User_Model_AuthAdapter($this->em(), '\User\Entity\User', 'email', 'password');
        $adapter->setIdentity($request['email'])
                ->setCredential($request['password']);

        $res = Zend_Auth::getInstance()->authenticate($adapter);

        if ($res->getCode() == Zend_Auth_Result::SUCCESS)
        {
            // write messages in session
            $session->write("messages", $res->getMessages());
            $session->write("messages_class", "success");

            // get redirect link from session
            $return_url = $session->read("return_url");
            if (! empty($return_url))
            {
                $session->write("return_url", null);
                return $this->redirect(urldecode($return_url));
            }
            else
                return $this->_helper->redirector->gotoRoute(array(), "home", true);
        }
        else
        {
            $session->write("messages", $res->getMessages());
            $session->write("messages_class", "error");

            $this->forward("new");
//            return $this->_helper->redirector->gotoRoute(array(), "login", true);
        }
    }

    public function logoutAction()
    {
        $this->em()->getRepository('\User\Entity\Session')->destroy();
        $session = $this->_helper->currentSession();
        $session->setUser(null);
        $this->em()->persist($session);
        $this->em()->flush();

        // messages in session
        $session->write("messages", "LOGGED_OUT_OK");
        $session->write("messages_class", "info");

        // redirect home
        $this->_helper->redirector->gotoRoute(array(), "home", true);
    }
}
