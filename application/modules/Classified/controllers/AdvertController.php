<?php

class Classified_AdvertController extends Bisna\Controller\Action
{
    // show new advert form
    public function newAction()
    {
        $this->view->advert = $advert = $this->_getParam('advert');

        if (! empty($advert['category']))
        {
            $cRepo = $this->em()->getRepository('\Classified\Entity\Category');
            $this->view->questions = $cRepo->getTiedQuestions($advert['category']);
        }
    }

    // create advert: proceed post query
    public function createAction()
    {
        $request = $this->getParam("advert");
        $session = $this->_helper->currentSession();
        $aRepo = $this->em()->getRepository('Classified\Entity\Advert');

        try
        {
            // validations
            $request = $aRepo->filter($request);
            $valid = $aRepo->validate($request);
            $messages = ($valid === true)? array() : $valid;

            if (! empty($messages))
            throw new Zend_Exception("Validation errors");

            // OK: create entity
            $advert = new \Classified\Entity\Advert();
            $advert->populate($request);
            $this->em()->persist($advert);
            $this->em()->flush();

            // messages to session
            $session->write("messages_class", "success");
            $session->write("messages", "ADVERT_CREATED_OK");

            $this->redirect($this->view->url(array("action"=>"show", "id"=>$advert->getId()), "adverts", true));
        }
        catch (\Zend_Exception $e)
        {
            // errors
            $session->write("messages", $messages);
            $session->write("messages_class", "error");

            $this->forward("new", null, null, array());
        }
    }

    // show edit advert form
    public function editAction()
    {
        $aRepo = $this->em()->getRepository('\Classified\Entity\Advert');
        $cRepo = $this->em()->getRepository('\Classified\Entity\Category');
        $id = $this->_getParam("id");
        $advert = $aRepo->find($id);

        // access
        if (is_null($advert)) {throw new Zend_Exception("ERROR", 404);}
        $this->_helper->checkMember(); // logged in
//        $this->_helper->checkOwner($advert->getUser()->getId()); // is ad owner || is admin

        $this->view->advert = $advert->getArrayCopy();
        $this->view->questions = $cRepo->getTiedQuestions($advert->getCategory()->getId());

    }

    public function updateAction()
    {
        $aRepo = $this->em()->getRepository('\Classified\Entity\Advert');
        $id = $this->_getParam("id");
        $advert = $aRepo->find($id);
        $request = $this->getParam("advert");
        $session = $this->_helper->currentSession();

        // access
        if (is_null($advert)) {throw new Zend_Exception("ERROR", 404);}
        $this->_helper->checkMember(); // logged in
//        $this->_helper->checkOwner($advert->getUser()->getId()); // is ad owner || is admin

        try
        {
            // validations
            $request = $aRepo->filter($request);
            $valid = $aRepo->validate($request);
            $messages = ($valid === true)? array() : $valid;

            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // OK: create entity
            $advert->populate($request);
            $this->em()->persist($advert);
            $this->em()->flush();

            // messages to session
            $session->write("messages_class", "success");
            $session->write("messages", "ADVERT_UPDATED_OK");

            $this->redirect($this->view->url(array("action"=>"show", "id"=>$advert->getId()), "adverts", true));
        }
        catch (\Zend_Exception $e)
        {
            // errors
            $session->write("messages", $messages);
            $session->write("messages_class", "error");

            $this->forward("edit", null, null, array());
        }
    }

    public function showAction()
    {
        $aRepo = $this->em()->getRepository('\Classified\Entity\Advert');
        $id = $this->_getParam("id");
        $this->view->advert = $advert = $aRepo->find($id);

        // access
        if (is_null($advert)) {throw new Zend_Exception("ERROR", 404);}
    }

    public function manageAction()
    {

    }

    // delete entity
    public function deleteAction()
    {
        $aRepo = $this->em()->getRepository('\Classified\Entity\Advert');
        $id = $this->getRequest()->getPost('id', 0);
        $advert = $aRepo->find($id);

        // access
        if (is_null($advert)) {throw new Zend_Exception("ERROR", 404);}
        $this->_helper->checkMember(); // logged in
//        $this->_helper->checkOwner($advert->getUser()->getId()); // is ad owner || is admin

        $this->em()->remove($advert);
        $this->em()->flush();

        $session = $this->_helper->currentSession();
        $session->write("messages_class", "info");
        $session->write("messages", "ADVERT_DELETED_OK");
        return $this->_helper->json(array("status"=>"OK"));
    }

    public function listAction()
    {
        $query = $this->getParam("query");
        $slug = $this->getParam("slug");

        if ($slug)
        {
            $category = $this->em()->createQuery("select c from \Classified\Entity\Category c
                    where c.domain =?1 and c.slug =?2")
                ->setParameter(1, CURRENT_DOMAIN)
                ->setParameter(2, $slug)
                ->setMaxResults(1)
                ->getResult();

            if (empty($category))
            {
                $category = null;
                echo "404";
            }
            else
                $category = $category[0];
        }
        else
            $category = null;


        if ($category)
            echo $category->getName();
    }
}
