<?php

class Classified_QuestionController extends Bisna\Controller\Action
{
    // shows new question form
    public function newAction()
    {
        $this->_helper->checkAdmin();
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        $this->view->question = $this->getParam("question");
    }
}