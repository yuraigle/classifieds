<?php

class Classified_Model_AdvertRepository extends \Doctrine\ORM\EntityRepository
{
    public function expr() {}

    public function filter($request)
    {
        return $request;
    }

    public function validate($request)
    {
        $messages = array();

        if (empty($request['title']))
            $messages[] = "ADVERT_TITLE_BLANK";

        if (empty($request['price']))
            $messages[] = "ADVERT_PRICE_BLANK";

        if (empty($request['category']))
            $messages[] = "ADVERT_CATEGORY_BLANK";

        return ($messages == array())? true : $messages;
    }
}