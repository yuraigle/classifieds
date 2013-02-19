<?php

class Classified_CategoryController extends Bisna\Controller\Action
{
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

            // clear category tree in cache
            $cache = \Zend_Registry::get('cache');
            $cache->remove('CATEGORIES_LIST');

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

    // update entity
    public function updateAction()
    {
        $id = $this->_getParam("id");
        $category = $this->em()->find('\Classified\Entity\Category', $id);
        $session = $this->_helper->currentSession();

        // access
        if (is_null($category)) {throw new Zend_Exception("ERROR", 404);}
        $this->_helper->checkAdmin();

        if($this->getRequest()->getMethod() === 'POST')
        {
            try
            {
                $request = $this->_getParam("category");

                // validations
                $valid = $this->em()->getRepository('\Classified\Entity\Category')
                    ->validate($request);
                $messages = ($valid === true)? array() : $valid;

                if (! empty($messages))
                    throw new Zend_Exception("Validation errors");

                // update entity
                $category->populate($request);
                $this->em()->persist($category);
                $this->em()->flush();

                // clear category tree in cache
                $cache = \Zend_Registry::get('cache');
                $cache->remove('CATEGORIES_LIST');

                // messages to session
                $session->write("messages_class", "success");
                $session->write("messages", "CATEGORY_UPDATED_OK");
            }
            catch (Zend_Exception $e)
            {
                // errors to session
                $session->write("messages", $messages);
                $session->write("messages_class", "error");
            }
                return $this->_helper->redirector->gotoRoute(array(
                    "module"=>"admin", "controller"=>"category", 
                    "action"=>"edit", "id"=>$id), "admin", true);
        }
    }

    // delete entity
    public function deleteAction()
    {
        $this->_helper->checkAdmin();

        $id = $this->_getParam("id");
        $category = $this->em()->find("\Classified\Entity\Category", $id);

        $this->em()->remove($category);
        $this->em()->flush();

        // clear category tree in cache
        $cache = \Zend_Registry::get('cache');
        $cache->remove('CATEGORIES_LIST');

        $session = $this->_helper->currentSession();
        $session->write("messages_class", "info");
        $session->write("messages", "CATEGORY_DELETED_OK");
        return $this->_helper->json(array("status"=>"OK"));
    }

    // render category fields
    public function renderAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        $id = $this->_getParam("id");

        $cRepo = $this->em()->getRepository('\Classified\Entity\Category');
        $this->view->questions = $cRepo->getTiedQuestions($id);
    }
}
