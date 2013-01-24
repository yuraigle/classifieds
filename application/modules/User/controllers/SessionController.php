<?php

class User_SessionController extends Bisna\Controller\Action
{
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

        if($this->getRequest()->getMethod() === 'POST' && ! $this->_getParam("status"))
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

        try
        {
            $res = $adapter->authenticate();

            if (! $res->isValid())
                throw new Zend_Auth_Adapter_Exception($res->getMessages()[0]);

            // logged in OK
            $user = $this->em()->find('\User\Entity\User', $res->getIdentity());
            $session->setUser($user);

            // write messages in session
            $session->write("messages", $res->getMessages());
            $session->write("messages_class", "success");

             $this->_helper->returnToSession(array(), "home", true);
        }
        catch (Zend_Auth_Adapter_Exception $e)
        {
            $session->write("messages", $e->getMessage());
            $session->write("messages_class", "error");

            $this->forward("new", null, null, array("user" => $request, "status" => "ERROR"));
        }
    }

    public function logoutAction()
    {
        // redirect if already not logged in
        if (! $this->_helper->currentUser())
            $this->_helper->redirector->gotoRoute(array(), "home", true);

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
