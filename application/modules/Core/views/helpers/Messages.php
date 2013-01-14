<?php

class Core_View_Helper_Messages extends Zend_View_Helper_Abstract
{
    public function messages ()
    {
        if (empty($this->view->messages))
            return null;

        if (! is_array($this->view->messages))
        {
            $messages_html = $this->view->tr($this->view->messages);
            $class = "alert alert-block";
        }
        elseif (count($this->view->messages)==1)
        {
            $messages_html = $this->view->tr($this->view->messages[0]);
            $class = "alert alert-block";
        }
        else
        {
            $messages_html = "<ul>";
            foreach ($this->view->messages as $msg)
                $messages_html .= "<li>" . $this->view->tr($msg) . "</li>";
            $messages_html .= "</ul>";
            $class = "alert alert-block";
        }

        if ($this->view->messages_class)
            $class .= " alert-" . $this->view->messages_class;

        return
            "<div class='$class'>" . 
            "<a class='close' data-dismiss='alert' href='#'>&times;</a>" .
            $messages_html . "</div>";
    }
}