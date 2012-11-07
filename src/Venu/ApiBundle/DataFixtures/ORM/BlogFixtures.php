<?php

namespace Venu\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Venu\ApiBundle\Entity\Blog;


class BlogFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
       
       
        
        $blog1 = new Blog();
        $blog1->setTitle('First one');
        $blog1->setBlog('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $blog1->setAuthor($manager->merge($this->getReference('user')));
        $blog1->setTags('blog, riktam');
        $blog1->setCreatedAt(new \DateTime());
        $blog1->setUpdatedAt($blog1->getCreatedAt());
        $manager->persist($blog1);

        $blog2 = new Blog();
        $blog2->setTitle('This is a very big big one');
        $blog2->setBlog('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $blog2->setAuthor($manager->merge($this->getReference('user')));
        $blog2->setTags('venu, gopal, tech');
        $blog2->setCreatedAt(new \DateTime("2012-10-1 06:12:33"));
        $blog2->setUpdatedAt($blog1->getCreatedAt());
        $manager->persist($blog2);

        $manager->flush();
        
        $this->addReference('blog-1', $blog1);
        $this->addReference('blog-2', $blog2);
    }
    
    public function getOrder()
    {
        return 2;
    }
}
