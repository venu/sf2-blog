<?php

namespace Venu\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $blogs = $em->getRepository('VenuApiBundle:Blog')
                    ->getLatestBlogs();

        return $this->render('VenuAdminBundle:Default:index.html.twig', array(
            'blogs' => $blogs
        ));
    }
}
