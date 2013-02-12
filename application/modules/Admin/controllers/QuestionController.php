<?php

class Admin_QuestionController extends Admin_Model_Controller
{
    // questions list
    public function indexAction()
    {
        $dql = "SELECT q from \Classified\Entity\Question q WHERE 1=1";

        $query = $this->em()->createQuery($dql);
        $adapter = new \Core_Model_PaginatorAdapter($query);
        $this->view->paginator = $paginator = new \Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($this->_getParam("page", 1));
    }

    // shows create question form (modal window)
    public function newAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        $this->view->question = $this->getParam("question");
    }

    // edit category form
    public function editAction()
    {
        $id = $this->_getParam("id");
        $category = $this->em()->find("\Classified\Entity\Category", $id);

        $this->view->category = $category->getArrayCopy();
    }

    // show remove question request (modal window)
    public function removeAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        $this->view->id = $this->_getParam("id");
    }

    // populate fake data
    public function populateAction()
    {
        // clear table
        $this->em()->createQuery('delete from \Classified\Entity\Category')->execute();

        $maxDepth = 3;
        $maxCount = 100;

        $faker = Faker\Factory::create();
        $categories = array();
        for ($i = 0; $i<$maxCount; $i++)
        {
            // 1/20 cats are top-level
            if (empty($categories) || rand(0, 100) < 5)
                $parent = null;
            else
            {
                $parentId = $categories[array_rand($categories)];
                $parent = $this->em()->find('\Classified\Entity\Category', $parentId);
            }

            $category = new \Classified\Entity\Category();
            $category->setName($faker->sentence(3));
            $category->setParent($parent);
            $this->em()->persist($category);
            $this->em()->flush();

            if ($category->getDepth() < $maxDepth-1)
                $categories[] = $category->getId();
        }

        // clear category tree in cache
        $cache = \Zend_Registry::get('cache');
        $cache->remove('CATEGORIES_LIST');

        $session = $this->_helper->currentSession();
        $session->write("messages", "Fake data created");
        $session->write("messages_class", "info");

        $this->_helper->redirector->gotoRoute(array("controller"=>"category"), "admin", true);
    }
}
