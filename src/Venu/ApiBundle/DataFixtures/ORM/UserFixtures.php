<?php
namespace Venu\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
    
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager'); 
        $user = $userManager->findUserByUsername('venu');
        if(!$user) {
            $userManager = $this->container->get('fos_user.user_manager'); 
            $user = $userManager->createUser(); 

            $user->setEmail('venu@riktamtech.com'); 
            $user->setUsername('venu');
            $user->setPlainPassword('12345');
            $user->addRole('ROLE_ADMIN');    
            $user->setEnabled(1);

            try{
                $manager->persist($user); 
                $manager->flush(); 
            }catch(Exception $e){
                echo 'Community fixtures error: ' . $e->getMessage();
            }
        }
        
        $this->addReference('user', $user);
    }
    
    public function getOrder()
    {
        return 1;
    }
}