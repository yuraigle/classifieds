<?php

class Classified_Model_Question extends \Doctrine\ORM\EntityRepository
{
    public function expr() {}

    public function validate($request)
    {
        $messages = array();

        if (empty($request['name']))
            $messages[] = "QUESTION_NAME_BLANK";

        return ($messages == array())? true : $messages;
    }
}