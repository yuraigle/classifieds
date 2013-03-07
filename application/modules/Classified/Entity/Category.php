<?php

namespace Classified\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Classified_Model_CategoryRepository")
 * @ORM\Table(name="categories")
 */
class Category extends \Core\Entity\Core
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $postable;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="CategoryQuestionReference", mappedBy="category")
     */
    protected $template;

    // =========================================================================
    public function populate($request)
    {
        $fields = array("name");
        foreach ($fields as $field)
            if (isset($request[$field]))
                $this->{$field} = $request[$field];

        if (isset($request['postable']))
            $this->postable =(boolean) $request['postable'];

        $this->_em = \Zend_Registry::get("em");
        $this->parent = $this->_em->find('\Classified\Entity\Category', $request['parent']);
    }

    public function getArrayCopy()
    {
        $arrayCopy = array();
        $fields = array("id", "name", "postable");
        foreach ($fields as $field)
            $arrayCopy[$field] = $this->{$field};

        $arrayCopy["parent"] = (is_null($this->parent))? 0 : $this->parent->getId();

        return $arrayCopy;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function defaultFields()
    {
        if (is_null($this->postable))
            $this->postable = false;
    }

    /*
     * Get category depth (number of parents)
     *
     * @return integer
     */
    public function getDepth()
    {
        $obj = $this;
        $depth = 0;

        while ($obj->getParent())
        {
            $obj = $obj->getParent();
            $depth++;
        }

        return $depth;
    }
    // =========================================================================

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Category
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
     * Add children
     *
     * @param \Classified\Entity\Category $children
     * @return Category
     */
    public function addChildren(\Classified\Entity\Category $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \Classified\Entity\Category $children
     */
    public function removeChildren(\Classified\Entity\Category $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Classified\Entity\Category $parent
     * @return Category
     */
    public function setParent(\Classified\Entity\Category $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \Classified\Entity\Category 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add template
     *
     * @param \Classified\Entity\CategoryQuestionReference $template
     * @return Category
     */
    public function addTemplate(\Classified\Entity\CategoryQuestionReference $template)
    {
        $this->template[] = $template;
    
        return $this;
    }

    /**
     * Remove template
     *
     * @param \Classified\Entity\CategoryQuestionReference $template
     */
    public function removeTemplate(\Classified\Entity\CategoryQuestionReference $template)
    {
        $this->template->removeElement($template);
    }

    /**
     * Get template
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add children
     *
     * @param \Classified\Entity\Category $children
     * @return Category
     */
    public function addChild(\Classified\Entity\Category $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Classified\Entity\Category $children
     */
    public function removeChild(\Classified\Entity\Category $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Set postable
     *
     * @param boolean $postable
     * @return Category
     */
    public function setPostable($postable)
    {
        $this->postable = $postable;

        return $this;
    }

    /**
     * Get postable
     *
     * @return boolean 
     */
    public function getPostable()
    {
        return $this->postable;
    }
}
