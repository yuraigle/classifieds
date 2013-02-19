<?php

namespace Classified\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Classified_Model_AnswerRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="answers")
 */
class Answer extends \Core\Entity\Core
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Question", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $question;

    /**
     * @ORM\ManyToOne(targetEntity="Advert", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="advert_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $advert;

    /**
     * @ORM\Column(type="text")
     */
    protected $val;

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
     * Set val
     *
     * @param string $val
     * @return Answer
     */
    public function setVal($val)
    {
        $this->val = $val;
    
        return $this;
    }

    /**
     * Get val
     *
     * @return string 
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * Set question
     *
     * @param \Classified\Entity\Question $question
     * @return Answer
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

    /**
     * Set advert
     *
     * @param \Classified\Entity\Advert $advert
     * @return Answer
     */
    public function setAdvert(\Classified\Entity\Advert $advert = null)
    {
        $this->advert = $advert;
    
        return $this;
    }

    /**
     * Get advert
     *
     * @return \Classified\Entity\Advert 
     */
    public function getAdvert()
    {
        return $this->advert;
    }
}