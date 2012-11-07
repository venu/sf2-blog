<?php
namespace Venu\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View AS FOSView; 


class BaseController extends Controller
{
    
    /**
     * Get the view object with success structure
     *
     * @param Array/Object
     *
     * @return FOSView
     */
    protected function getSuccessView($data = '', $code = '200')
    {
        return FOSView::create(array(
            'success' => true,
            'data'    => $data
        ), $code);
    }
    
    /**
     * Get the view object with error structure 
     *
     * @param Array/Object/String
     *
     * @return FOSView
     */
    protected function getErrorView($errors = '', $code = '400')
    {
        return FOSView::create(array(
            'success' => false,
            'error'    => $errors
        ), $code);
    }

   /**
     * Get the validation errors
     *
     * @param ConstraintViolationList $errors Validator error list
     *
     * @return FOSView
     */
    protected function getValidationErrorsView($errors)
    {
        $msgs = array();
        $it = $errors->getIterator();

        foreach ($it as $val) {
            $msg = $val->getMessage();
            $params = $val->getMessageParameters();
            
            //using FOSUserBundle translator domain 'validators'
            $msgs[$val->getPropertyPath()][] = $this->get('translator')->trans($msg, $params, 'validators');
        }
        
        $data = array(
            "status"=> "error",
            "status_code"=> 400,
            "status_text"=> "Bad Request",
            "current_content"=> "",
            "message"=> $msgs
        );
        
        $view = FOSView::create($data);
        $view->setStatusCode(400);
        return $view;
    }
    
    /*
     * @param User $user
     * Update session when the user logins or refresh his token
     * 
     * return Boolean
     */
    protected function updateSession($user){
        $session = $this->get("session");
        $session->set("user",$user);
        
        //update user id in db
        $em = $this->get('doctrine')->getEntityManager();
        $sessionObj = $em->getRepository('VenuApiBundle:Session')->find($session->getId());
        if($sessionObj){
            $sessionObj->setUserId($user->getId());
        }else{
            $sessionObj = new \Venu\ApiBundle\Entity\Session();
            $sessionObj->setUserId($user->getId());
            $sessionObj->setSessionId($session->getId());
            $sessionObj->setSessionValue('');
            $sessionObj->setSessionTime('');
        }
        $em->persist($sessionObj);
        $em->flush();
        
        return true;
    }
    
    protected function _generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    /*
     * as per http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     */
    protected function _getCulture(){
        $langs = array();
        $finalLang = 'de';

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // break up string into pieces (languages and q factors) - by comma
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

            if (count($lang_parse[1])) {
                // create a list like "en" => 0.8
                $langs = array_combine($lang_parse[1], $lang_parse[4]);

                // set default to 1 for any without q factor
                foreach ($langs as $lang => $val) {
                    if ($val === '') $langs[$lang] = 1;
                }

                // sort list based on value	
                arsort($langs, SORT_NUMERIC);
            }
        }

        // look through sorted list and use first one that matches our languages
        foreach ($langs as $lang => $val) {
            if (strpos($lang, 'de') === 0) {
                 $finalLang = 'de';
            } else if (strpos($lang, 'en') === 0) {
                 $finalLang = 'en';
            } 
        }

        return $finalLang;
    }
}