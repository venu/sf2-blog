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
        
        if ($this->getRequest()->getMethod() == 'POST') {
            $blog->setAuthor($this->get('security.context')->getToken()->getUser());
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()
                           ->getEntityManager();
                $em->persist($blog);
                $em->flush();

                return $this->redirect($this->generateUrl('VenuAdminBundle_homepage'));
            }
            
        }

        return $this->render('VenuAdminBundle:Blog:form.html.twig', array(
            'blog' => $blog,
            'form'   => $form->createView()
        ));
       
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();

        $blog = $em->getRepository('VenuApiBundle:Blog')->find($id);
        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }
        
        $form = $this->createForm(new BlogType(), $blog);
        
        if ($this->getRequest()->getMethod() == 'POST') {
            $blog->setAuthor($this->get('security.context')->getToken()->getUser());
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()
                           ->getEntityManager();
                $em->persist($blog);
                $em->flush();

                return $this->redirect($this->generateUrl('VenuBlogBundle_blog_show', array(
                    'id'    => $blog->getId(),
                    'slug'  => $blog->getSlug()))
                );
            }
            
        }

        return $this->render('VenuAdminBundle:Blog:editform.html.twig', array(
            'blog' => $blog,
            'form'   => $form->createView()
        ));
    }
    
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();

        $blog = $em->getRepository('VenuApiBundle:Blog')->find($id);
        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }
        
        $em->remove($blog);
        $em->flush();
        
        return $this->redirect($this->generateUrl('VenuAdminBundle_homepage') );
    }
}
