<?php

class Core_View_Helper_AddChosen extends Zend_View_Helper_Abstract
{
    public function addChosen()
    {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . "/css/chosen.css");
        $this->view->headScript()->appendFile($this->view->baseUrl() . "/js/chosen.jquery.min.js");
        $this->view->headScript()->appendScript("$('.chzn-select').chosen()");
    }
}