<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\User_Model_Session")
 * @ORM\Table(name="sessions")
 */
class Session extends \Core\Entity\Core implements \Pike_Session_Entity_Interface
{
    /**
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\Column(name="data", type="text", nullable=false)
     */
    protected $data;

    /**
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    protected $modified;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setdata($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setModified(\DateTime $date)
    {
        $this->modified = $date;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public static function getModifiedFieldName()
    {
        return 'modified';
    }
}