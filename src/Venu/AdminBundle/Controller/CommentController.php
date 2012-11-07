<?php

namespace Venu\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Venu\ApiBundle\Entity\Blog;
use Venu\AdminBundle\Form\BlogType;

class CommentController extends Controller
{   
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $blog = $em->getRepository('VenuApiBundle:Blog')->find($id);
        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }
        
        $comments = $em->getRepository('VenuApiBundle:Comment')
                       ->getCommentsForBlog($blog->getId());
        
        return $this->render('VenuAdminBundle:Comment:show.html.twig', array(
            'blog'      => $blog,
            'comments'  => $comments
        ));       
    }
    
    public function deleteAction($id, $blogId)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();
        
        $blog = $em->getRepository('VenuApiBundle:Blog')->find($blogId);
        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $comment = $em->getRepository('VenuApiBundle:Comment')->find($id);
        if (!$comment) {
            throw $this->createNotFoundException('Unable to find comment.');
        }
        
        $em->remove($comment);
        $em->flush();
        
         $this->get('session')->setFlash('blogger-notice', 'Successfully deleted!');
        
         
        return $this->redirect($this->generateUrl('VenuAdminBundle_blog_comments', array(
                    'id'    => $blog->getId()))
                );
        return $this->redirect($this->generateUrl('VenuAdminBundle_homepage') );
    }
}
