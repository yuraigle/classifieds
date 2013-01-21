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

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

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

    /**
     * Set user
     *
     * @param \User\Entity\User $user
     * @return Session
     */
    public function setUser(\User\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \User\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public function write($key, $value)
    {
        $data = \Zend_Auth::getInstance()->getStorage()->read();

        if (is_null($value))
            unset($data[$key]);
        else
            $data[$key] = $value;

        \Zend_Auth::getInstance()->getStorage()->write($data);
    }

    public function read($key)
    {
        $data = \Zend_Auth::getInstance()->getStorage()->read();
        return (isset($data[$key]))? $data[$key] : null;
    }
}