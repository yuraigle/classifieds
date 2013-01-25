<?php

class Classified_CategoryController extends Bisna\Controller\Action
{
    // shows create category form: for ajax use
    public function newAction()
    {
        $this->_helper->checkAdmin();
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        $this->view->category = $this->getParam("category");
    }

    // create category
    public function createAction()
    {
        $this->_helper->checkAdmin();
        $request = $this->getParam("category");
        $session = $this->_helper->currentSession();

        try
        {
            // validations
            $valid = $this->em()->getRepository('Classified\Entity\Category')
                ->validate($request);
            $messages = ($valid === true)? array() : $valid;
            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // OK: create entity
            $category = new \Classified\Entity\Category();
            $category->populate($request);
            $this->em()->persist($category);
            $this->em()->flush();

            // messages to session
            $session->write("messages_class", "success");
            $session->write("messages", "CATEGORY_CREATED_OK");

            // json context
            if ($this->_getParam("format") == "json")
                return $this->_helper->json(array("status"=>"OK"));
            // other contexts are not defined yet
        }
        catch (\Zend_Exception $e)
        {
            // errors
            $session->write("messages", $messages);
            $session->write("messages_class", "error");

            if ($this->_getParam("format") == "json")
                return $this->_helper->json(array("status"=>"ERROR",
                    "messages"=>$this->view->messages()));
            // other contexts are not defined yet
        }
    }

    // show edit category form -> admin
    public function editAction()
    {

    }

    public function deleteAction()
    {
        $this->_helper->checkAdmin();

        $id = $this->_getParam("id");
        $category = $this->em()->find("\Classified\Entity\Category", $id);

        $this->em()->remove($category);
        $this->em()->flush();

        $session = $this->_helper->currentSession();
        $session->write("messages_class", "info");
        $session->write("messages", "CATEGORY_DELETED_OK");
        return $this->_helper->json(array("status"=>"OK"));
    }
}
