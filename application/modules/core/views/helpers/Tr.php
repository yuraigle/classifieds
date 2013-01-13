<?php

class Core_View_Helper_Tr extends Zend_View_Helper_Translate
{
    public function tr($messageid = null)
    {
        return parent::translate($messageid);
    }
}