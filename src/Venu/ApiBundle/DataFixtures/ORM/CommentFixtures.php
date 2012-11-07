<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Venu\ApiBundle\Entity\Comment;
use Venu\ApiBundle\Entity\Blog;

class CommentFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
       
        
        $comment = new Comment();
        $comment->setUser($manager->merge($this->getReference('user')));
        $comment->setComment('I love symfony');
        $comment->setBlog($manager->merge($this->getReference('blog-1')));
        $manager->persist($comment);

        $comment = new Comment();
        $comment->setUser($manager->merge($this->getReference('user')));
        $comment->setComment('I love symfony2');
        $comment->setBlog($manager->merge($this->getReference('blog-1')));
        $manager->persist($comment);

        $comment = new Comment();
        $comment->setUser($manager->merge($this->getReference('user')));
        $comment->setComment('This is great');
        $comment->setBlog($manager->merge($this->getReference('blog-2')));
        $manager->persist($comment);

        $comment = new Comment();
        $comment->setUser($manager->merge($this->getReference('user')));
        $comment->setComment('This is great2 ');
        $comment->setBlog($manager->merge($this->getReference('blog-2')));
        $comment->setCreatedAt(new \DateTime("2011-07-23 06:15:20"));
        $manager->persist($comment);

        $comment = new Comment();
        $comment->setUser($manager->merge($this->getReference('user')));
        $comment->setComment('This is great3');
        $comment->setBlog($manager->merge($this->getReference('blog-2')));
        $comment->setCreatedAt(new \DateTime("2011-07-23 06:18:35"));
        $manager->persist($comment);
        
        $comment = new Comment();
        $comment->setUser($manager->merge($this->getReference('user')));
        $comment->setComment('This is great3');
        $comment->setBlog($manager->merge($this->getReference('blog-2')));
        $comment->setCreatedAt(new \DateTime("2011-07-23 06:22:53"));
        $manager->persist($comment);
        

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
