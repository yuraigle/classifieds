<?php

class Classified_Model_CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function expr() {}

    public function validate($request)
    {
        $messages = array();

        if (empty($request['name']))
            $messages[] = "CATEGORY_NAME_BLANK";

        return ($messages == array())? true : $messages;
    }

    public function getTiedQuestions($cat_id)
    {
        return $this->_em->createQuery('
            select q from \Classified\Entity\Question q
            where q.id in (
                select distinct q2.id
                from \Classified\Entity\CategoryQuestionReference r
                left join r.question q2
                where r.category = ?1
                order by r.weight desc
            )')
            ->setParameter(1, $cat_id)
            ->getResult();
    }
}
