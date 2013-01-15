<?php

class User_ProfileController extends Bisna\Controller\Action
{
    // show user profile
    public function indexAction()
    {
        // user exists
        $id = $this->_getParam("id");
        $user = $this->em()->find('\User\Entity\User', $id);
        if (is_null($user)) {throw new Zend_Exception("ERROR", 404);}

        $this->view->user = $user->getArrayCopy();
    }

    // edit user profile
    public function editAction()
    {
        // user exists
        $id = $this->_getParam("id");
        $user = $this->em()->find('\User\Entity\User', $id);
        if (is_null($user)) {throw new Zend_Exception("ERROR", 404);}

        $this->_helper->checkMember(); // logged in
        $this->_helper->checkOwner($id); // is profile owner || is admin

        if($this->getRequest()->getMethod() === 'POST' && !$this->_getParam("status"))
            return $this->forward('update');

        $this->view->messages = $this->_getParam("messages");
        $this->view->messages_class = $this->_getParam("messages_class");
        $this->view->user = $this->_getParam("user", $user->getArrayCopy());
    }

    public function updateAction()
    {
        // user exists
        $request = $this->_getParam("user");
        $user = $this->em()->find('\User\Entity\User', $request['id']);
        if (is_null($user)) {throw new Zend_Exception("ERROR", 404);}

        $this->_helper->checkMember(); // logged in
        $this->_helper->checkOwner($request['id']); // is profile owner || is admin

        try
        {
            $valid = $this->em()->getRepository('User\Entity\User')->validate($request, $password_req = false);

            $messages = ($valid === true)? array() : $valid;
            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // update user
            $user->populate($request);
            $this->em()->persist($user);
            $this->em()->flush();

            $this->_helper->messages("PROFILE_UPDATED_OK", "success");
            $this->_helper->redirector->gotoRoute(array("id"=>$request['id']), "profile", true);
        }
        catch (Zend_Exception $e)
        {
            $options = array(
                "status" => "error",
                "messages" => $messages,
                "messages_class" => "error",
            );
            $this->forward("edit", null, null, $options);
        }
    }
}