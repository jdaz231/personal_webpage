<?php
namespace Controllers;

require_once 'BaseController.php';

use Respect\Validation\Validator as v;
use Models\User;
use Zend\Diactoros\Response\RedirectResponse;

class AuthController extends BaseController{
    public function getLogin(){
        $responseMessage = null;
        return $this->renderHTML('login.twig',[
            'responseMessage'=>$responseMessage
        ]);
    }

    public function postLogin($request){
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $user = User::where('email',$postData['email'])->first();
        if ($user) {
            if (\password_verify($postData['password'] , $user->password)) {
                $_SESSION['userId'] = $user->id;
                return new RedirectResponse('/personal_webpage/admin');
            }else {
                $responseMessage = 'Bad credentials'; 
            }
        } else {

            $responseMessage = 'Bad credentials';
        }
        
        return $this->renderHTML('login.twig',[
            'responseMessage' => $responseMessage
        ]);
    }

    public function getLogout(){
        unset($_SESSION['userId']);
        return new RedirectResponse('/personal_webpage/login');
    }
}