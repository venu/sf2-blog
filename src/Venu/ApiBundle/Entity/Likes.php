<?php

namespace Venu\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Venu\ApiBundle\Entity\Likes
 *
 * @ORM\Table(name="likes")
 * @ORM\Entity(repositoryClass="Venu\ApiBundle\Repository\LikesRepository")
 */
class Likes
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
   
    /**
     * @ORM\ManyToOne(targetEntity="Blog", inversedBy="likes")
     * @ORM\JoinColumn(name="blog_id", referencedColumnName="id")
     */
    protected $blog;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;
    
     /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return Likes
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     * @return Likes
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set blog
     *
     * @param Venu\ApiBundle\Entity\Blog $blog
     */
    public function setBlog(\Venu\ApiBundle\Entity\Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * Get blog
     *
     * @return Venu\ApiBundle\Entity\Blog 
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * Set user
     *
     * @param Venu\ApiBundle\Entity\User $user
     * @return Likes
     */
    public function setUser(\Venu\ApiBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Venu\ApiBundle\Entity\FosUser 
     */
    public function getUser()
    {
        return $this->user;
    }
}