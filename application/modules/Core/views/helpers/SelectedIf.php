<?php

class Core_View_Helper_SelectedIf extends Zend_View_Helper_Abstract
{
    public function selectedIf($bool)
    {
        return ($bool)? " selected" : "";
    }
}