<?php

namespace Venu\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $blogs = $em->getRepository('VenuApiBundle:Blog')
                    ->getLatestBlogs();

        return $this->render('VenuBlogBundle:Page:index.html.twig', array(
            'blogs' => $blogs
        ));
    }
    
    public function aboutAction()
    {
        return $this->render('VenuBlogBundle:Page:about.html.twig');
    }
   
}