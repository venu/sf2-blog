<?php
namespace Venu\ApiBundle\Repository;

use Venu\ApiBundle\Entity\Likes;
use Venu\ApiBundle\Entity\User;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

use Venu\ApiBundle\Exception\HttpException;


class LikesRepository extends EntityRepository 
{
    
    public function getLikesCount($blog) {
        $query = $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->add('where', 'u.blog = ?1')
               ->setParameter(1, $blog);
        
        return  $query->getQuery()->getSingleScalarResult();
    }
    
    //get Users likes
    public function getLikes($blog, $offset=0, $limit=20)
    {
        try{
            //Create query builder for users table
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);
            
            $qb->select(array('c','u'));
            $qb->from('Venu\ApiBundle\Entity\Likes', 'c');
            $qb->Join('c.user', 'u');
            $qb->add('orderBy', 'c.id asc');
            
            $qb->add('where','c.blog = ?1')
               ->setParameter(1, $designPageId);
        
            //Get our query
            $q = $qb->getQuery();
            
            $q->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, true);
            
            //Return result
            return $q->getResult();

        }catch(Exception $e){
            $this->get('logger')->err($e->getTraceAsString());
            throw new HttpException(500, "Some database error occured");	
        }
    }
    
   

	
}