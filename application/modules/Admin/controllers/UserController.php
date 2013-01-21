<?php

class Admin_UserController extends Admin_Model_Controller
{
    // users list
    public function indexAction()
    {
        $this->view->active_tab = "users";

        $query = $this->em()->createQuery("select u from \User\Entity\User u where u.deleted = 0");

        // doctrine2 pagination
        $d2_paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $adapter =  new \Zend_Paginator_Adapter_Iterator($d2_paginator->getIterator());
        $this->view->paginator = $paginator = new \Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page = $this->_getParam("page", 1));
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_Paginator::setDefaultItemCountPerPage(30);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(array('paginationControl.phtml', 'Core'));
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
