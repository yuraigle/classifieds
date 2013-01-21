<?php

class Admin_IndexController extends Admin_Model_Controller
{
    public function indexAction()
    {
        $this->view->active_tab = "home";
    }
}
