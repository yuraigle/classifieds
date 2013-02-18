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

    // edit category form (modal window)
    public function editAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('Layout');

        $id = $this->_getParam("id");
        $question = $this->em()->find("\Classified\Entity\Question", $id);
        $this->view->question = $question->getArrayCopy();
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
        $this->em()->createQuery('delete from \Classified\Entity\Question')->execute();
        $this->em()->createQuery('delete from \Classified\Entity\CategoryQuestionReference')->execute();
        $categories = $this->em()->createQuery('select c from \Classified\Entity\Category c where c.parent is not null')
            ->getResult();

        $faker = Faker\Factory::create();

        for ($i = 0; $i < 200; $i++)
        {
            $question = new \Classified\Entity\Question();
            $type = $faker->randomElement(array ('text', 'textarea', 'select', 'ranged', 'checkbox'));
            $question->setType($type);
            $question->setName($faker->sentence(2));
            $question->setDescription($faker->text);
            $question->setRequired($faker->boolean);

            if ($type == 'select')
            {
                $count = (integer) rand(2, 8);
                $question->setPredefined(join("\n", $faker->words($count)));
            }

            $this->em()->persist($question);

            $cat1 = $faker->randomElement($categories);
            $ref = new \Classified\Entity\CategoryQuestionReference();
            $ref->setQuestion($question);
            $ref->setCategory($cat1);
            $ref->setWeight((integer) rand(1, 10));
            $this->em()->persist($ref);

            $cat2 = $faker->randomElement($categories);
            if ($cat2->getId() != $cat1->getId())
            {
                $ref = new \Classified\Entity\CategoryQuestionReference();
                $ref->setQuestion($question);
                $ref->setCategory($cat1);
                $ref->setWeight((integer) rand(1, 10));
                $this->em()->persist($ref);
            }
        }

        $this->em()->flush();

        $session = $this->_helper->currentSession();
        $session->write("messages", "Fake data created");
        $session->write("messages_class", "info");

        $this->_helper->redirector->gotoRoute(array("controller"=>"question"), "admin", true);
    }
}
