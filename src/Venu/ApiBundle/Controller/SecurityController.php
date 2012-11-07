<?php
namespace Venu\ApiBundle\Controller;

use Venu\ApiBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View AS FOSView;

use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Validator\ConstraintViolation;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;


use Venu\ApiBundle\Exception\HttpException;
use Venu\ApiBundle\Exception\NotFoundHttpException;
use Venu\ApiBundle\Exception\AccessDeniedException as AccessDeniedException;


/**
 * Controller that provides Restfuls security functions.
 *
 * @Prefix("/api")
 * @NamePrefix("pictureplix_api_user_security_")
 */
class SecurityController extends BaseController
{

    /**
     * WSSE Token generation - Login
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="_username", default="", description="Email.")
     * @RequestParam(name="_password", default="", description="Password.")
     *
     * @return FOSView
     * @throws AccessDeniedException
     * @ApiDoc()
     */
    public function postTokenCreateAction(ParamFetcher $paramFetcher)
    {
        $view = FOSView::create();

        $username = $paramFetcher->get('_username');
        $password = $paramFetcher->get('_password');

        $um = $this->get('fos_user.user_manager');
        $user = $um->findUserByUsernameOrEmail($username);

        if (!$user instanceof User) {
            throw new HttpException(400, "Invalid User credentials");
        }
        
        if (!$this->checkUserPassword($user, $password)) {
            throw new HttpException(400, "Invalid User credentials");
        }
        
        if (!$user->isEnabled()) {
            throw new HttpException(400, "You should activate account first as per the instructions in the mail we sent.");
        }
        
        //set response & headers
        $header = $this->getTokenHeader($user);
        $view->setHeader("Authorization", 'WSSE profile="UsernameToken"');
        $view->setHeader("X-WSSE", $header);
        
        //create session
        //$this->updateSession($user);
        
        //send the token back to client
        $data = array('WSSE' => $header);
        $view->setStatusCode(200)->setData($data);
        return $view;
    }
    
     /**
     * WSSE Token Remove - logout
     *
     * @return FOSView
     * @ApiDoc()
     */
    public function deleteTokenDestroyAction()
    {
        $view = FOSView::create();
        $security = $this->get('security.context');
        $token = new AnonymousToken(null, new User());
        $security->setToken($token);
        $this->get('session')->invalidate();
        $view->setStatusCode(200)->setData(array('message'=>'Logout successful'));
        return $view;
    }
    
    
    /**
     * Creates a new User - Register.
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="name", default="", description="Name.")
     * @RequestParam(name="email",  default="", description="Email.")
     * @RequestParam(name="password",  default="", description="Plain Password.")
     *
     * @return FOSView
     * @ApiDoc()
     */
    public function postPublicUserAction(ParamFetcher $paramFetcher)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername($paramFetcher->get('email'));
        $user->setEmail($paramFetcher->get('email'));
        $user->setPlainPassword($paramFetcher->get('password'));
        $user->setName($paramFetcher->get('name'));
        $user->addRole('ROLE_USER');
        
        //Use validation group 'Registration' THE FOSUserBundle
        $validator = $this->get('validator');        
        $errors = $validator->validate($user, array('Registration'));
        if (count($errors) == 0) {
            $user->setConfirmationToken($this->_generateRandomString(5));
            //$user->setConfirmationToken($this->container->get('fos_user.util.token_generator')->generateToken());
            $userManager->updateUser($user);
            
            $this->container->get('router')->getContext()->setHost($this->container->getParameter('website_url'));
            $this->container->get('router')->getContext()->setBaseUrl('/' . $this->_getCulture());
            $this->container->get('fos_user.mailer')->sendConfirmationEmailMessage($user);
            
            $view = FOSView::create($user, 200);
        } else {
            $view = $this->getValidationErrorsView($errors);
        }
        return $view;
    }
    
   
     /**
     * Re-Sends an activation email.
     *
     * @return FOSView
     * @ApiDoc()
     */
    public function postPublicUserResendemailAction()
    {
        $username = $this->getRequest()->get('email');
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);
 
        if (null === $user) {
           throw new HttpException(404, "User does not exists");
        }
        
        if ($user->isEnabled()) {
            throw new HttpException(403,"User has already enabled his account");
        }
        
        $this->container->get('router')->getContext()->setHost($this->container->getParameter('website_url'));
        $this->container->get('router')->getContext()->setBaseUrl('/' . $this->_getCulture());
        //$user->setConfirmationToken($this->container->get('fos_user.util.token_generator')->generateToken());
        $user->setConfirmationToken($this->_generateRandomString(5));
        $this->container->get('fos_user.mailer')->sendConfirmationEmailMessage($user);
        $this->container->get('fos_user.user_manager')->updateUser($user);
        
        return FOSView::create($user, 200);
    }
    
     /**
     * Activate an account with confirmation token.
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="token", requirements="[0-9A-Za-z]+", default="", description="Email verification token.")
     * @return FOSView
     * @ApiDoc()
     */
    public function postPublicUserEmailverifyAction(ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($paramFetcher->get('token'));

        if (null === $user) {
            throw new HttpException(404, sprintf('The user with confirmation token "%s" does not exist', $paramFetcher->get('token')));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setLastLogin(new \DateTime());

        $this->container->get('fos_user.user_manager')->updateUser($user);
        
        //set response & headers
        $header = $this->getTokenHeader($user);
        $view = FOSView::create();
        $view->setHeader("Authorization", 'WSSE profile="UsernameToken"');
        $view->setHeader("X-WSSE", $header);
       
        $data = array('WSSE' => $header);
        $view->setStatusCode(200)->setData($data);
        return $view;
    }
    
     /**
     * Reset password request.
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="email", requirements=".*", default="", description="User Email.")
     * @throws HttpException
     * @return FOSView
     * @ApiDoc()
     */
    public function postPublicUserForgotpasswordAction(ParamFetcher $paramFetcher){
        $username = $paramFetcher->get('email');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            throw new HttpException(404, sprintf('The user "%s" does not exist', $paramFetcher->get('email')));
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            throw new HttpException(400, 'The password for this user has already been requested within the last 24 hours.');
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            //$tokenGenerator = $this->container->get('fos_user.util.token_generator');
            //$tokenGenerator->generateToken();
            $user->setConfirmationToken($this->_generateRandomString(5));
        }
        
        $this->container->get('router')->getContext()->setHost($this->container->getParameter('website_url'));
        $this->container->get('router')->getContext()->setBaseUrl('/' . $this->_getCulture());
            
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);
        
        return FOSView::create($user, 200);
    }
    
    /**
     * Change Password.
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="token", requirements="[0-9A-Za-z]+", default="", description="Token.")
     * @RequestParam(name="new", requirements=".*", default="", description="password.")
     * @RequestParam(name="repeated", requirements=".*", default="", description="confirm password.")
     * @return FOSView
     * @ApiDoc()
     */
    public function postPublicUserResetpasswordAction(ParamFetcher $paramFetcher){
        $token = $paramFetcher->get('token');

        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new HttpException(404, sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            throw new HttpException(400, 'The password request has expired.');
        }
        
        if($paramFetcher->get('new') != $paramFetcher->get('repeated')){
             throw new HttpException(400, 'Passwords doesn\'t match.');
        }
        
        //Use validation group 'Registration' THE FOSUserBundle
        $user->setPlainPassword($paramFetcher->get('new'));
        
        $validator = $this->get('validator');        
        $errors = $validator->validate( $user, array('ResetPassword'));
        if (count($errors) == 0) {
            $user->setPlainPassword($paramFetcher->get('new'));
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setEnabled(true);
            $this->container->get('fos_user.user_manager')->updateUser($user);
        
            $view = FOSView::create($user, 200);
        } else {
            $view = $this->getValidationErrorsView($errors);
        }
        return $view;
    }
    
    protected function getTokenHeader($user)
    {
        $created = date('c');
        $nonce = substr(md5(uniqid('nonce_', true)), 0, 16);
        $nonceHigh = base64_encode($nonce);
        $passwordDigest = base64_encode(sha1($nonce . $created . $user->getPassword(), true));
        return "UsernameToken Username=\"{$user->getUsername()}\", PasswordDigest=\"{$passwordDigest}\", Nonce=\"{$nonceHigh}\", Created=\"{$created}\"";
    }
    
    protected function checkUserPassword(User $user, $password)
    {
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }
   
}