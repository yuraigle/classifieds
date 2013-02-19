<?php

class Classified_Model_AnswerRepository extends \Doctrine\ORM\EntityRepository
{
    public function expr() {}

    public function validate($request)
    {
        $messages = array();

        return ($messages == array())? true : $messages;
    }
}