<?php

class User_SessionController extends Bisna\Controller\Action
{
    public function loginAction()
    {
        // redirect if already logged in
        if ($this->_helper->currentUser())
        {
            $this->_helper->messages("ALREADY_LOGGED_IN");
            $this->_helper->redirector->gotoRoute(array(), "home", true);
        }

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
                $session = $this->_helper->currentSession();
                $session->setUser($user);
                $this->em()->persist($session);
                $this->em()->flush();

                // messages in session
                $this->_helper->messages("LOGGED_IN_OK", "success");
                $this->_helper->redirector->gotoRoute(array(), "home", true);
            }
            catch (Zend_Exception $e)
            {
                $this->view->messages = $messages;
                $this->view->messages_class = "error";
            }
        }

        $this->view->user = $user = $this->_getParam("user");
    }

    public function logoutAction()
    {
        $this->em()->getRepository('\User\Entity\Session')->destroy();
        $session = $this->_helper->currentSession();
        $session->setUser(null);
        $this->em()->persist($session);
        $this->em()->flush();

        // redirect somewhere
        $this->_helper->messages("LOGGED_OUT_OK", "info");
        $this->_helper->redirector->gotoRoute(array(), "home", true);
    }
}
