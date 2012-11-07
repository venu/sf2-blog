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

use Venu\ApiBundle\Entity\Comment;

use Venu\ApiBundle\Exception\HttpException;
use Venu\ApiBundle\Exception\NotFoundHttpException;

/**
 * Controller that provides Restful sercies over the resource Users.
 *
 * @NamePrefix("pictureplix_api_comments_")
 */
class CommentsController extends BaseController
{
    /**
     * Returns all comments list for a design page.
     * 
     * @param integer $designPageId Design Page Id
     * @param ParamFetcher $paramFetcher Paramfetcher
     * 
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Starting position.")
     * @QueryParam(name="limit", requirements="\d+", default="20", description="How many results needed in the response.")
     *
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc(resource=true)
     */
    public function getDesignpageCommentsAction($designPageId, ParamFetcher $paramFetcher)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $comments = $em->getRepository('VenuApiBundle:Comment')->getComments(
            $designPageId,
            $paramFetcher->get('offset'),
            $paramFetcher->get('limit')
        );
        
        return FOSView::create($comments, 200);
    }
    
    /**
     * Post a comment for a design page.
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     * 
     * @param integer $designPageId Design Page Id
     * @RequestParam(name="comment", requirements="[a-z]+", default="", description="Comment") 
     *
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc()
     */
    public function postDesignpageCommentsAction($designPageId, ParamFetcher $paramFetcher)
    {
        $em = $this->get('doctrine')->getEntityManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$paramFetcher->get('comment')){
            throw new HttpException(400, "Please enter comment");
        }
        
        $designPage = $em->getRepository('VenuApiBundle:DesignPage')->find($designPageId);
        if(!$designPage){
            throw new NotFoundHttpException("Design page doesn't exist");
        }
        
        $commentObj = new Comment();
        $commentObj->setComment($paramFetcher->get('comment'));
        $commentObj->setUser($user);
        $em->persist($commentObj);
 
        $designPage->addComment($commentObj);
        $em->persist($designPage);
        $em->flush();
      
        return FOSView::create(array("message"=>"Successfully Created!"), 200);
    }
    
    /**
     * Delete comment.
     *
     * @param integer $designPageId Design Page Id
     * @param integer $commentId Comment Id
     *
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc()
     */
    public function deleteDesignpageCommentsAction($designPageId, $commentid)
    {
        $em = $this->get('doctrine')->getEntityManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $commentObj = $em->getRepository('VenuApiBundle:Comment')
                        ->findOneBy(array('user' => $user->getId(), 'designPage' => $designPageId, 'id'=>$commentid));
        if(!$commentObj){
            throw new NotFoundHttpException("Comment doesn't exist");
        }
        
        $em->remove($commentObj);
        $em->flush();
        
        return FOSView::create(array("message"=>"Successfully Deleted!"), 200);
    }

 

}