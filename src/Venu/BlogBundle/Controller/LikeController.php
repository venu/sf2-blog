<?php

namespace Venu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Venu\ApiBundle\Entity\Likes;

/**
 * Like controller.
 */
class LikeController extends Controller
{
    
    public function addAction($id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $isLiked = $em->getRepository('VenuApiBundle:Likes')->isLiked($user, $id);
        if ($isLiked) {
            throw $this->createNotFoundException('You have already liked.');
        }
        
        $blog = $em->getRepository('VenuApiBundle:Blog')->find($id);
        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }
        
        $like = new Likes();
        $like->setUser($user);
        $like->setBlog($blog);
        $em->persist($like);
        $em->flush();
        
        $this->get('session')->setFlash('blogger-notice', 'Successfully Liked!');
        
        return $this->redirect($this->generateUrl('VenuBlogBundle_blog_show', array(
                    'id'    => $blog->getId(), 'slug'    => $blog->getSlug()))
                );
    }
    
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $like = $em->getRepository('VenuApiBundle:Likes')->isLiked($user, $id);
        if (!count($like)) {
            throw $this->createNotFoundException('You haven\'t liked yet.');
        }
        
        $likeObj = $em->getRepository('VenuApiBundle:Likes')->find($like[0]['id']);
        if (!$likeObj) {
            throw $this->createNotFoundException('Unable to find your like.');
        }
        $em->remove($likeObj);
        $em->flush();
        
        $blog = $em->getRepository('VenuApiBundle:Blog')->find($id);
        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }
        
         $this->get('session')->setFlash('blogger-notice', 'Successfully deleted!');
        
         
         return $this->redirect($this->generateUrl('VenuBlogBundle_blog_show', array(
                    'id'    => $blog->getId()))
                );
    }
}