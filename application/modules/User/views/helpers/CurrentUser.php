<?php

class User_View_Helper_CurrentUser extends Zend_View_Helper_Abstract
{
    public function currentUser()
    {
        $data = Zend_Auth::getInstance()->getStorage()->read();

        if (empty($data['id']))
            return null;

        $em = \Zend_Registry::get("em");
        $user = $em->find('\User\Entity\User', $data['id']);

        return $user;
    }
}
