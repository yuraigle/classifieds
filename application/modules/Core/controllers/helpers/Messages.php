<?php

class Core_Controller_Helper_Messages extends Zend_Controller_Action_Helper_Abstract
{
    public function messages($messages, $messages_class)
    {
        $storage = Zend_Auth::getInstance()->getStorage();
        $data = $storage->read();
        $data["messages"] = $messages;
        $data["messages_class"] = $messages_class;

        $storage->write($data);
    }

    public function direct($messages, $messages_class)
    {
        $this->messages($messages, $messages_class);
    }
}