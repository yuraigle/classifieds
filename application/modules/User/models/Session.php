<?php

class User_Model_Session
{


    public static function destroy()
    {
        Zend_Auth::getInstance()->getStorage()->clear();
        Zend_Session::forgetMe();
    }

    public function set($option, $value)
    {
        $storage = Zend_Auth::getInstance()->getStorage();
        $storage->write(array_merge(array($option => $value), $storage->read()));

        Zend_Session::rememberMe(1209600); // 2 weeks
    }


}
