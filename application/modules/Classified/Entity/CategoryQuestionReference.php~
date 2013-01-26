<?php

namespace Classified\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories_questions")
 */
class CategoryQuestionReference extends \Core\Entity\Core
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="template", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Question", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $question;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weight;

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
     * Set weight
     *
     * @param integer $weight
     * @return CategoryQuestionReference
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set category
     *
     * @param \Classified\Entity\Category $category
     * @return CategoryQuestionReference
     */
    public function setCategory(\Classified\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \Classified\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set question
     *
     * @param \Classified\Entity\Question $question
     * @return CategoryQuestionReference
     */
    public function setQuestion(\Classified\Entity\Question $question = null)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return \Classified\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }
}