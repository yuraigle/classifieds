<?php

class Core_Plugin_Header extends Zend_Controller_Plugin_Abstract
{
    private $_view;
    private $_templates;

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view)
            $viewRenderer->initView();

        $this->_view = $viewRenderer->view;
        $this->_templates = include(APPLICATION_PATH . "/configs/templates.php");

        $this->_addJs("/js/jquery.min.js");
        $this->_addJs("/js/bootstrap.min.js");
        $this->_addJs("/js/jquery.lightbox.min.js");
        $this->_addJs("/js/core.js");
        $this->_addJs("/js/custom.js");

        $this->_addCss("/css/core.min.css");
        $this->_addCss("/css/custom.min.css");
        $this->_addCss("/css/jquery.lightbox.css");

        if ($request->getModuleName() == 'Admin')
            $this->_addCss("/css/admin.min.css");

        // override template
        $id = $request->getModuleName() . ":" . $request->getControllerName() . ":" . $request->getActionName();
        if (! empty($this->_templates[CURRENT_DOMAIN]) &&
            in_array($id, $this->_templates[CURRENT_DOMAIN]))
            $viewRenderer->setViewSuffix(strtolower(CURRENT_DOMAIN) . ".phtml");
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        // override layout
        $layout = Zend_Layout::getMvcInstance();
        if (! empty($this->_templates[CURRENT_DOMAIN]) &&
            in_array($layout->getLayout(), $this->_templates[CURRENT_DOMAIN]))
            $layout->setViewSuffix(strtolower(CURRENT_DOMAIN) . ".phtml");
    }

    protected function _addJs($file)
    {
        $this->_view->headScript()->appendFile($this->_view->baseUrl() . $file);
    }

    protected function _addCss($file)
    {
        $this->_view->headLink()->appendStylesheet($this->_view->baseUrl() . $file);
    }
}