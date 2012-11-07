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


use Venu\ApiBundle\Exception\HttpException;
use Venu\ApiBundle\Exception\NotFoundHttpException;

/**
 * Controller that provides Restful sercies over the resource Users.
 *
 * @NamePrefix("pictureplix_api_users_")
 */
class UsersController extends BaseController
{

    /**
     * Returns the overall user list.
     * 
     * @param ParamFetcher $paramFetcher Paramfetcher
     * 
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Starting position.")
     * @QueryParam(name="limit", requirements="\d+", default="20", description="How many results needed in the response.")
     * @QueryParam(name="search", requirements="[a-z]+", default="", description="search word")
     *
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc(resource=true)
     */
    public function getUsersAction(ParamFetcher $paramFetcher)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $users = $em->getRepository('VenuApiBundle:User')->getusers(
            $paramFetcher->get('offset'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('search')    
        );
        
        return FOSView::create($users, 200);
    }
    
    /**
     * Returns an user by Id.
     *
     * @param string $id To get logged in user details, pass 'me' or pass the Id to get the user details.
     *
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc()
     */
    public function getUserAction($id)
    {
        if($id == 'me'){
            $user = $this->get('security.context')->getToken()->getUser();
        }else{
            $em = $this->get('doctrine')->getEntityManager();
            $user = $em->getRepository('VenuApiBundle:User')->find($id); 
        }
        
        if ($user) {
            return FOSView::create($user, 200);
        } else {
            throw new NotFoundHttpException("User does not exists");
        }
    }


    /**
     * Update an user by id.
     *
     * @param integer $id Id
     * @Secure(roles="ROLE_USER")
     * @return FOSView
     * @ApiDoc()
     */
    public function putUserAction($id)
    {
        $request = $this->getRequest();
        $userManager = $this->container->get('fos_user.user_manager');
        
        $em = $this->get('doctrine')->getEntityManager();
        $user = $em->getRepository('VenuApiBundle:User')->find($id);
        //$user = $userManager->findByEmail($id);
        if (!$user) {
            throw new NotFoundHttpException("User does not exists");
        }

        if ($request->get('name')) {
            $user->setName($request->get('name'));
        }
        if ($request->get('gender')) {
            $user->setGender($request->get('gender'));
        }
        if ($request->get('location')) {
            $user->setLocation($request->get('location'));
        }
        if ($request->get('dob')) {
            $user->setDob( new \DateTime($request->get('dob')));
        }
        if ($request->get('password')) {
            if (!$request->get('new_password')) {
                throw new HttpException(400,"Some parameters missing");
            }
            
            //compare the password
            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);
            $encoded_pass = $encoder->encodePassword($request->get('new_password'), $user->getSalt());
            if($encoded_pass != $user->getPassword()){
                throw new HttpException(400,"Password not matching");
            }
            
            $user->setPlainPassword($request->get('new_password'));
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($user, array('Profile'));
        if (count($errors) == 0) {
            $userManager->updateUser($user);
            $view = FOSView::create();
            $view->setStatusCode(200);
        } else {
            $view = $this->getValidationErrorsView($errors);
        }
        return $view;
    }
    
     /**
     * Refresh WSSE Token
     *
     * @return FOSView
     * @Secure(roles="ROLE_USER")
     * @ApiDoc()
     */
    public function getTokenRefreshAction()
    {
        $view = FOSView::create();
        $user = $this->get('security.context')->getToken()->getUser();
        
        //set response & headers
        $created = date('c');
        $nonce = substr(md5(uniqid('nonce_', true)), 0, 16);
        $nonceHigh = base64_encode($nonce);
        $passwordDigest = base64_encode(sha1($nonce . $created . $user->getPassword(), true));
        $header = "UsernameToken Username=\"{$user->getEmail()}\", PasswordDigest=\"{$passwordDigest}\", Nonce=\"{$nonceHigh}\", Created=\"{$created}\"";
        $view->setHeader("Authorization", 'WSSE profile="UsernameToken"');
        $view->setHeader("X-WSSE", $header);
        
        //create session
        $this->updateSession($user);
        
        //send the token back to client
        $data = array('WSSE' => $header);
        $view->setStatusCode(200)->setData($data);
        return $view;
    }

}