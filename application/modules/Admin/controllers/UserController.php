<?php

class Admin_UserController extends Admin_Model_Controller
{
    // users list
    public function indexAction()
    {
        $dql = "SELECT u from \User\Entity\User u WHERE u.deleted=0";

        $query = $this->em()->createQuery($dql);
        $adapter = new \Core_Model_PaginatorAdapter($query);
        $this->view->paginator = $paginator = new \Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam("page", 1));
    }

    // show profile edit form in modal window
    public function editAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');

        $user = $this->em()->find("\User\Entity\User", $this->_getParam("id"));
        if (is_null($user)) {throw new Zend_Exception("ERROR", 404);}

        $this->view->user = $user->getArrayCopy();
    }
}
