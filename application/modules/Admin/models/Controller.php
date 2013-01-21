<?php
/**
 * Created by JetBrains PhpStorm.
 * User: aigle
 * Date: 1/20/13
 * Time: 3:28 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class Admin_Model_Controller extends Bisna\Controller\Action
{
    public function init()
    {
        $this->_helper->checkAdmin();

        Zend_Layout::startMvc()->setLayout('admin');

        parent::init();
    }

    public function postDispatch()
    {
        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout'))
        {
            $response = $this->getResponse();
            $response->insert('navbar', $this->view->render('navbar.phtml'));
        }
    }
}
