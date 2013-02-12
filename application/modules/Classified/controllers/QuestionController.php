<?php

class Classified_QuestionController extends Bisna\Controller\Action
{
    // shows new question form
    public function createAction()
    {
        $this->_helper->checkAdmin();
        $request = $this->getParam("question");
        $session = $this->_helper->currentSession();

        try
        {
            // validations
            $valid = $this->em()->getRepository('Classified\Entity\Question')
                ->validate($request);
            $messages = ($valid === true)? array() : $valid;

            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // OK: create entity
            $question = new \Classified\Entity\Question();
            $question->populate($request);
            $this->em()->persist($question);
            $this->em()->flush();

            // messages to session
            $session->write("messages_class", "success");
            $session->write("messages", "QUESTION_CREATED_OK");

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

    // delete entity
    public function deleteAction()
    {
        $this->_helper->checkAdmin();

        $id = $this->_getParam("id");
        $question = $this->em()->find("\Classified\Entity\Question", $id);

        $this->em()->remove($question);
        $this->em()->flush();

        $session = $this->_helper->currentSession();
        $session->write("messages_class", "info");
        $session->write("messages", "QUESTION_DELETED_OK");
        return $this->_helper->json(array("status"=>"OK"));
    }
}
