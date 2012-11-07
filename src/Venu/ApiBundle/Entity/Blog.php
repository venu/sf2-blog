<?php

namespace Venu\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Venu\ApiBundle\Repository\BlogRepository")
 * @ORM\Table(name="blog")
 * @ORM\HasLifecycleCallbacks()
 */
class Blog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $slug;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    protected $author;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $blog;
    
    
    /**
     * @ORM\Column(type="text")
     */
    protected $tags;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="blog")
     */
    protected $comments;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\prePersist
     */
    public function setCreatedValue()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\preUpdate
     */
    public function setUpdatedValue()
    {
       $this->setUpdatedAt(new \DateTime());
    }
    
    public function __toString()
    {
        return $this->getTitle();
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv'))
        {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;

        $this->setSlug($this->title);
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
     * Set author
     *
     * @param string $author
     */
    public function setAuthor(\Venu\ApiBundle\Entity\user $author)
    {
        $this->author = $author;
    }

    /**
     * Get author
     *
     * @return Venu\ApiBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set blog
     *
     * @param text $blog
     */
    public function setBlog($blog)
    {
        $this->blog = $blog;
    }

    /**
     * Get blog
     *
     * @return text 
     */
    public function getBlog($length = null)
    {
        if (false === is_null($length) && $length > 0)
            return substr($this->blog, 0, $length);
        else
            return $this->blog;
    }

    /**
     * Set image
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set tags
     *
     * @param text $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get tags
     *
     * @return text 
     */
    public function getTags()
    {
        return $this->tags;
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
     * Add comments
     *
     * @param Venu\ApiBundle\Entity\Comment $comments
     */
    public function addComments(\Venu\ApiBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $this->slugify($slug);
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add comments
     *
     * @param Venu\ApiBundle\Entity\Comment $comments
     */
    public function addComment(\Venu\ApiBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    }
}