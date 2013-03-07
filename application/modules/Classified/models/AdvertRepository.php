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
            $messages[] = "ADVERT_ERROR_TITLE_BLANK";

        if (empty($request['price']))
            $messages[] = "ADVERT_ERROR_PRICE_BLANK";
        elseif(round(floatval($request['price']), 2) == 0)
            $messages[] = "ADVERT_ERROR_PRICE_WRONG_FORMAT";

        if (empty($request['category']))
            $messages[] = "ADVERT_ERROR_CATEGORY_BLANK";
        else {
            $category = $this->_em->find('\Classified\Entity\Category', $request['category']);
            if (is_null($category))
                $messages[] = "ADVERT_ERROR_CATEGORY_NOT_FOUND";
            elseif (! $category->getPostable())
                $messages[] = "ADVERT_ERROR_CATEGORY_NOT_POSTABLE";
        }

        return ($messages == array())? true : $messages;
    }
}