<?php

class Core_View_Helper_Messages extends Zend_View_Helper_Abstract
{
    public function messages()
    {
        if (! empty($this->view->messages))
        {
            // messages set in controller
            $messages = $this->view->messages;
            $messages_class = $this->view->messages_class;
        }
        else
        {
            // get messages from session
            $data = Zend_Auth::getInstance()->getStorage()->read();
            if (! empty($data['messages']))
            {
                $messages = $data['messages'];
                $messages_class = @$data['messages_class'];

                unset($data['messages']);
                unset($data['messages_class']);

                Zend_Auth::getInstance()->getStorage()->write($data);
            }
        }

        if (empty($messages))
            return null;

        if (! is_array($messages))
            $messages_html = $this->view->tr($messages);
        elseif (count($messages)==1)
            $messages_html = $this->view->tr($messages[0]);
        else
        {
            $messages_html = "<ul>";
            foreach ($messages as $msg)
                $messages_html .= "<li>" . $this->view->tr($msg) . "</li>";
            $messages_html .= "</ul>";
        }

        $class = "alert alert-block";
        if (!empty($messages_class))
            $class .= " alert-" . $messages_class;

        return
            "<div class='$class'>" . 
            "<a class='close' data-dismiss='alert' href='#'>&times;</a>" .
            $messages_html . "</div>";
    }
}