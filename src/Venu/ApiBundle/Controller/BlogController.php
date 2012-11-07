<?php
namespace Venu\ApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;                  // @ApiDoc(resource=true, description="Filter",filters={{"name"="a-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}})
use FOS\RestBundle\Controller\Annotations\NamePrefix;       // NamePrefix Route annotation class @NamePrefix("bdk_core_user_userrest_")
use FOS\RestBundle\View\RouteRedirectView;                  // Route based redirect implementation
use FOS\RestBundle\View\View AS FOSView;                    // Default View implementation.
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use JMS\SecurityExtraBundle\Annotation\Secure;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;

use Venu\ApiBundle\Entity\Blog;
use Venu\AdminBundle\Form\BlogType;

use Venu\ApiBundle\Exception\HttpException;
use Venu\ApiBundle\Exception\NotFoundHttpException;

/**
 * Controller that provides Restful sercies over the resource Users.
 *
 * @NamePrefix("pictureplix_api_blog_")
 */
class BlogController extends BaseController
{
    /**
     * Returns all blog posts
     *
     * @return FOSView
     * @ApiDoc(resource=true)
     */
    public function getBlogsAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $blogs = $em->getRepository('VenuApiBundle:Blog')
                    ->getLatestBlogs();
        
        return FOSView::create($blogs, 200);
    }
    
    /**
     * Creates a new Blog - need admin previlage
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="title", requirements=".*", default="", description="Title.")
     * @RequestParam(name="blog", requirements=".*",  default="", description="Text.")
     * @RequestParam(name="tags",  requirements=".*", default="", description="tags - comma seperated.")
     *
     * @return FOSView
     * @Secure(roles="ROLE_ADMIN")
     * @ApiDoc()
     */
    public function postBlogsAction(ParamFetcher $paramFetcher)
    {
        $blog = new Blog();
        $form = $this->createFormBuilder($blog, array('csrf_protection' => false))  
            ->add('title')
            ->add('blog')
            ->add('tags') 
           ->getForm();  
        
        //$form   = $this->createForm(new BlogType(), $blog);
        if(trim($paramFetcher->get('title')) == '') {
            throw new HttpException(400, 'Enter title.');
        }
        
         if(trim($paramFetcher->get('blog')) == '') {
            throw new HttpException(400, 'Enter blog.');
        }
        
        
         if(trim($paramFetcher->get('tags')) == '') {
            throw new HttpException(400, 'Enter tags.');
        }
        
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            $blog->setBlog($paramFetcher->get('blog'));
            $blog->setTags($paramFetcher->get('tags'));
            $blog->setTitle($paramFetcher->get('title'));
            $blog->setAuthor($this->get('security.context')->getToken()->getUser());

            if ($form->isValid()) {
                $em = $this->getDoctrine()
                           ->getEntityManager();
                $em->persist($blog);
                $em->flush();
                
            } else {
                return FOSView::create($this->getErrorMessages($form), 400);;
            }
            
        }
      
        return FOSView::create($blog, 200);
    }
    
    
    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach($parameters as $var => $value){
                $template = str_replace($var, $value, $template);
            }

            $errors[$key] = $template;
        }
        if ($form->hasChildren()) {
            foreach ($form->getChildren() as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                }
            }
        }

        return $errors;
    }
 
 

}