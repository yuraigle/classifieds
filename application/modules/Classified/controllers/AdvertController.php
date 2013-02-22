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

            // messages to session
            $session->write("messages_class", "success");
            $session->write("messages", "ADVERT_CREATED_OK");

            $this->redirect("/");
        }
        catch (\Zend_Exception $e)
        {
            // errors
            $session->write("messages", $messages);
            $session->write("messages_class", "error");

            $this->forward("new", null, null, array());
        }
    }
}
