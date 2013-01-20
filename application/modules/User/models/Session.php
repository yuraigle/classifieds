<?php

class User_Model_Session extends \Doctrine\ORM\EntityRepository
{
    public function expr() {}

    public function validate($request)
    {
        $messages = array();

        if (empty($request['email']))
            $messages[] = "EMAIL_BLANK";
        if (empty($request['password']))
            $messages[] = "PASSWORD_BLANK";

        return ($messages == array())? true : $messages;
    }

    public function write($data)
    {
        $storage = Zend_Auth::getInstance()->getStorage();
        $storage->write($data);
    }

    public function append($key, $value)
    {
        $storage = Zend_Auth::getInstance()->getStorage();
        $data = $storage->read();
        $data[$key] = $value;
        $storage->write($data);
    }

    public function destroy()
    {
        Zend_Auth::getInstance()->getStorage()->clear();
    }
}
