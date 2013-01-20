<?php

class User_RegistrationController extends Bisna\Controller\Action
{
    public function signupAction()
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

                $valid = $this->em()->getRepository('User\Entity\User')
                    ->validate($request);

                $messages = ($valid === true)? array() : $valid;
                if (! empty($messages))
                    throw new Zend_Exception("Validation errors");

                // create user
                $user = new User\Entity\User();
                $user->populate($request);
                $this->em()->persist($user);

                // write in session
                $session = $this->_helper->currentSession();
                $session->setUser($user);
                $this->em()->persist($session);
                $this->em()->flush();

                // messages in session
                $this->_helper->messages("SIGNED_UP_OK", "success");
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
}
