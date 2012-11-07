<?php
namespace Venu\ApiBundle\Repository;

use Venu\ApiBundle\Entity\User;
use Venu\ApiBundle\Entity\Friend;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

use Venu\ApiBundle\Exception\HttpException;


class UserRepository extends EntityRepository 
{
    
    public function getTotalCount($search = '') {
        $query = $this->createQueryBuilder('u')
                ->select('COUNT(u.id)');
        
        if($search){
            $query->add('where', 'u.name LIKE ?1')
            ->setParameter(1, '%'. $search .'%');
        }
        
        return  $query->getQuery()->getSingleScalarResult();
    }
    
    //get Users
    public function getUsers($offset=0, $limit=20, $search = '')
    {
        try{
            //Create query builder for users table
            $qb = $this->createQueryBuilder('u');
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);
            $qb->add('orderBy', 'u.name asc');
            
            if($search){
                $qb->add('where', 'u.name LIKE ?1')
                ->setParameter(1, '%'. $search .'%');
            }
             
            //Get our query
            $q = $qb->getQuery();
            
            //Return result
            return $q->getResult();

        }catch(Exception $e){
            $this->get('logger')->err($e->getTraceAsString());
            throw new HttpException(500, "Some database error occured");	
        }
    } 
    
    private function _isValidUser($user){
        if(is_int($user)){
            $userId = $user;
        } else if($user instanceof User){
            $userId = $user->getId();
        } else {
            throw new HttpException(400,'Bad Request');
        }
        
        return $userId;
    }
    



	
}