<?php

namespace Venu\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Venu\ApiBundle\Entity\Blog;
use Venu\AdminBundle\Form\BlogType;

class BlogController extends Controller
{   
    public function createAction()
    {
        $blog = new Blog();
        $form   = $this->createForm(new BlogType(), $blog);

        return $this->render('VenuAdminBundle:Blog:form.html.twig', array(
            'blog' => $blog,
            'form'   => $form->createView()
        ));
       
    }
    
    public function editAction()
    {
        $blog = new Blog();
        $form   = $this->createForm(new BlogType(), $blog);

        return $this->render('VenuAdminBundle:Blog:form.html.twig', array(
            'blog' => $blog,
            'form'   => $form->createView()
        ));
       
    }
    
    public function deleteAction()
    {
       
    }
}
