<?php

class Admin_CategoryController extends Admin_Model_Controller
{
    public function indexAction()
    {
        $dql = "SELECT c from \Classified\Entity\Category c WHERE 1=1";

        $query = $this->em()->createQuery($dql);
        $adapter = new \Core_Model_PaginatorAdapter($query);
        $this->view->paginator = $paginator = new \Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam("page", 1));
    }

    public function editAction()
    {
        $id = $this->_getParam("id");
        $category = $this->em()->find("\Classified\Entity\Category", $id);

        $this->view->category = $category->getArrayCopy();
    }

    // show remove category request (modal window)
    public function removeAction()
    {
        $this->_helper->checkAdmin();
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        $this->view->id = $this->_getParam("id");
    }
}
