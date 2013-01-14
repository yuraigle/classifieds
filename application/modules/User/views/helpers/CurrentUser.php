<?php

class User_View_Helper_CurrentUser extends Zend_View_Helper_Abstract
{
    public function currentUser()
    {
        $data = Zend_Auth::getInstance()->getStorage()->read();

        if (empty($data['id']))
            return null;

        $em = Zend_Registry::get("em"); // should not use this in view

        // return $em->find("\Entity\User", $data['id']);
    }
}
