<?php

class Core_View_Helper_CheckedIf extends Zend_View_Helper_Abstract
{
    public function checkedIf($bool)
    {
        return ($bool)? " checked" : "";
    }
}