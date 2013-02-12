<?php

namespace Classified\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Classified_Model_Question")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="questions")
 */
class Question extends \Core\Entity\Core
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $predefined;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $required;

    public function populate($request)
    {
        $fields = array("name", "type", "predefined", "description");
        foreach ($fields as $field)
            if (isset($request[$field]))
                $this->{$field} = $request[$field];
    }

    /**
     * @ORM\PrePersist
     */
    public function defaultFields()
    {
        if (is_null($this->required))
            $this->required = false;

        if ($this->type != 'select')
            $this->predefined = null;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Question
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Question
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set predefined
     *
     * @param string $predefined
     * @return Question
     */
    public function setPredefined($predefined)
    {
        $this->predefined = $predefined;
    
        return $this;
    }

    /**
     * Get predefined
     *
     * @return string 
     */
    public function getPredefined()
    {
        return $this->predefined;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Question
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return Question
     */
    public function setRequired($required)
    {
        $this->required = $required;
    
        return $this;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }
}