<?php

class Core_View_Helper_ActiveIf extends Zend_View_Helper_Abstract
{
    public function activeIf($bool)
    {
        return ($bool)? " class=\"active\"" : "";
    }
}