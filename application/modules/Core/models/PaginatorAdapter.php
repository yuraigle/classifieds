<?php

class Core_Model_PaginatorAdapter extends Doctrine\ORM\Tools\Pagination\Paginator
    implements Zend_Paginator_Adapter_Interface
{
    public function __construct($query, $fetchJoinCollection = true)
    {
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_Paginator::setDefaultItemCountPerPage(30);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(array('paginationControl.phtml', 'Core'));

        parent::__construct($query, $fetchJoinCollection);
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $this->getQuery()->setFirstResult($offset)->setMaxResults($itemCountPerPage);
        return $this->getQuery()->getResult($this->getQuery()->getHydrationMode());
    }
}