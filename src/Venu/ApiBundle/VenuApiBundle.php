<?php

namespace Venu\ApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VenuApiBundle extends Bundle
{
//	public function getParent()
//	{
//		return "FOSUserBundle";
//	}
        
        /*
         * Doctrine - MYSQL - ENUM fix
         * http://docs.doctrine-project.org/projects/doctrine-orm/en/2.1/cookbook/mysql-enums.html
         */
        public function boot() { 
            $em = $this->container->get('doctrine')->getEntityManager(); 
            $platform = $em->getConnection()->getDatabasePlatform(); 
            $platform->registerDoctrineTypeMapping('enum', 'string'); 
        }
}
