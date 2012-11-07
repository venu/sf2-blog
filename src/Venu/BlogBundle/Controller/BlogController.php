<?php

namespace Venu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Blog controller.
 */
class BlogController extends Controller
{
    /**
     * Show a blog entry
     */
    public function showAction($id, $slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $blog = $em->getRepository('VenuApiBundle:Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }
        
        $comments = $em->getRepository('VenuApiBundle:Comment')
                       ->getCommentsForBlog($blog->getId());
        
        return $this->render('VenuBlogBundle:Blog:show.html.twig', array(
            'blog'      => $blog,
            'comments'  => $comments
        ));
    }
}