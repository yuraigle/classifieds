<?php

namespace Classified\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Classified_Model_AdvertRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="adverts")
 */
class Advert extends \Core\Entity\Core
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
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $manufacturer;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $article;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $used;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Classified\Entity\Category", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="advert")
     */
    protected $answers;

    // =========================================================================
    public function populate($request)
    {
        $fields = array("title", "description", "price", "manufacturer",
            "article", "phone");

        foreach ($fields as $field)
            if (isset($request[$field]))
                $this->{$field} = $request[$field];

        $this->_em = \Zend_Registry::get("em");
        if (isset($request['used']))
            $this->used =(boolean) $request['used'];
        if (isset($request['category']))
            $this->category = $this->_em->find('\Classified\Entity\Category', $request['category']);

        $this->updateAnswers($request['answers']);
    }

    public function updateAnswers($request_ans)
    {
        $answers = $this->getAnswers();

        if (! empty($answers))
        {
            foreach ($answers as $ans)
            {
                if (empty($request_ans[$ans->getQuestion()->getId()]))
                    $this->_em->remove($ans);
                elseif ($request_ans[$ans->getQuestion()->getId()] != $ans->getVal())
                {
                    $ans->setVal($request_ans[$ans->getQuestion()->getId()]);
                    $this->_em->persist($ans);
                }

                unset($request_ans[$ans->getQuestion()->getId()]);
            }
        }

        if (! empty($request_ans))
            foreach ($request_ans as $key => $value)
            {
                $answer = new \Classified\Entity\Answer();
                $answer->setAdvert($this);
                $question = $this->_em->find('\Classified\Entity\Question', $key);

                if (! $question) {continue;}

                $answer->setQuestion($question);
                $answer->setVal($value);

                $this->_em->persist($answer);
            }
    }

    public function getArrayCopy()
    {
        $copy = parent::getArrayCopy();
        $copy['category'] = $this->getCategory()->getId();

        $answers = array();
        foreach ($this->getAnswers() as $answer)
            $answers[$answer->getQuestion()->getId()] = $answer->getVal();
        $copy['answers'] = $answers;

        return $copy;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function defaultFields()
    {
        if (is_null($this->used))
            $this->used = false;
    }
    // =========================================================================

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
     * Set title
     *
     * @param string $title
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Advert
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
     * Set price
     *
     * @param float $price
     * @return Advert
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set manufacturer
     *
     * @param string $manufacturer
     * @return Advert
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    
        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return string 
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set article
     *
     * @param string $article
     * @return Advert
     */
    public function setArticle($article)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return string 
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set used
     *
     * @param boolean $used
     * @return Advert
     */
    public function setUsed($used)
    {
        $this->used = $used;
    
        return $this;
    }

    /**
     * Get used
     *
     * @return boolean 
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Advert
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set user
     *
     * @param \User\Entity\User $user
     * @return Advert
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

    /**
     * Set category
     *
     * @param \Classified\Entity\Category $category
     * @return Advert
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
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add answers
     *
     * @param \Classified\Entity\Answer $answers
     * @return Advert
     */
    public function addAnswer(\Classified\Entity\Answer $answers)
    {
        $this->answers[] = $answers;
    
        return $this;
    }

    /**
     * Remove answers
     *
     * @param \Classified\Entity\Answer $answers
     */
    public function removeAnswer(\Classified\Entity\Answer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Advert
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }
}