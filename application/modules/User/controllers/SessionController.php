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
        $this->em()->getRepository('\User\Entity\Session')->destroy();

        // redirect somewhere
        $this->_helper->messages("LOGGED_OUT_OK", "info");
        $this->_helper->redirector->gotoRoute(array(), "home", true);
    }

    public function checkAction()
    {
        $request = $this->_getParam("user");
        try
        {
            // check for !empty fields
            $valid = $this->em()->getRepository('\User\Entity\Session')->validate($request);

            $messages = ($valid===true)? array():$valid;
            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // check for email&password match
            $user = $this->em()->getRepository('\User\Entity\User')
                ->authenticate($request['email'], $request['password']);

            $messages = (is_a($user, '\User\Entity\User'))? array():$user;
            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // write data in session
            $data = array("id" => $user->getId());
            $this->em()->getRepository('\User\Entity\Session')->write($data);

            $this->_helper->messages("LOGGED_IN_OK", "success");
            $this->_helper->redirector->gotoRoute(array(), "home", true);
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
