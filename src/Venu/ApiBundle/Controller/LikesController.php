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

use Venu\ApiBundle\Entity\Likes;


use Venu\ApiBundle\Exception\HttpException;
use Venu\ApiBundle\Exception\NotFoundHttpException;

/**
 * Controller that provides Restful sercies over the resource Users.
 *
 * @NamePrefix("pictureplix_api_likes_")
 */
class LikesController extends BaseController
{

    /**
     * Returns all Likes list for a design page.
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
    public function getDesignpageLikesAction($designPageId, ParamFetcher $paramFetcher)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $comments = $em->getRepository('VenuApiBundle:Likes')->getLikes(
            $designPageId,
            $paramFetcher->get('offset'),
            $paramFetcher->get('limit')
        );
        
        return FOSView::create($comments, 200);
    }
    
    
    /**
     * Returns all Likes list for a design page.
     * 
     * @param integer $designPageId Design Page Id
   
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc()
     */
    public function getDesignpageLikescountAction($designPageId)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $likesCount = $em->getRepository('VenuApiBundle:Likes')->getLikesCount(
            $designPageId
        );
        
        return FOSView::create(array("count"=>$likesCount), 200);
    }
    
    /**
     * Post a Like for a design page.
     *
     * @param integer $designPageId Design Page Id
     *
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc()
     */
    public function postDesignpageLikesAction($designPageId)
    {
        $em = $this->get('doctrine')->getEntityManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        //get design
        $designPage = $em->getRepository('VenuApiBundle:DesignPage')->find($designPageId);
        if(!$designPage){
            throw new NotFoundHttpException("Design page doesn't exist");
        }
        
        //don't allow post user to like his page
        $ownerUser = $designPage->getDesign()->getOrder()->getUser();
        if($ownerUser->getId() == $user->getId()){
            throw new HttpException(403, "You are not allowed to like your design!");
        }
       
        //check design allows user to like
        if($designPage->getDesign()->getOrder()->getShareLevel() == Order::SHARE_LEVEL_PRIVATE){
            throw new HttpException(403, "You are not allowed to like this design!");
        }
        
        //check whether he has already liked this one
        $likeObj = $em->getRepository('VenuApiBundle:Likes')
                        ->findOneBy(array('user' => $user->getId(), 'designPage' => $designPageId));
        if($likeObj){
            throw new HttpException(403, "You have aleady liked!");
        }
          
        //hurrayyyy he crossed all barriers        
        //create like
        $newLikeObj = new Likes();
        $newLikeObj->setUser($user);
        $em->persist($newLikeObj);
 
        $designPage->addLike($newLikeObj);
        $em->persist($designPage);
        
        //todo if max diamonds reached, dont give the diamonds
        
        //create diamond
        $diamondObj = $em->getRepository('VenuApiBundle:Diamond')->findOneByType(Diamond::TYPE_LIKE);
        if(!$diamondObj){
            throw new HttpException(500, "Some error with diamond type!");
        }
        
        $diamondhistoryObj = new DiamondHistory();
        $diamondhistoryObj->setUser($ownerUser);
        $diamondhistoryObj->setLike($newLikeObj);
        $diamondhistoryObj->setScore($diamondObj->getScore());
        $diamondhistoryObj->setDiamond($diamondObj);
        $diamondhistoryObj->setNote('{user} liked {designPageId}');
        $diamondhistoryObj->setType(DiamondHistory::TYPE_LIKE);
        $em->persist($diamondhistoryObj);
        
        $em->flush();
      
        return FOSView::create(array("message"=>"Successfully Created!"), 200);
    }
    
    /**
     * Delete Like.
     *
     * @param integer $designPageId Design Page Id
     *
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc()
     */
    public function deleteDesignpageLikesAction($designPageId)
    {
        $em = $this->get('doctrine')->getEntityManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $likeObj = $em->getRepository('VenuApiBundle:Likes')
                        ->findOneBy(array('user' => $user->getId(), 'designPage' => $designPageId));
        if(!$likeObj){
            throw new NotFoundHttpException("Like doesn't exist");
        }
        
        $em->remove($likeObj);
        $em->flush();
        
        return FOSView::create(array("message"=>"Successfully Deleted!"), 200);
    }

    
    
 

}