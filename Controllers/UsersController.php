<?php
namespace Controllers;
use Respect\Validation\Validator as v;
require_once 'BaseController.php';
use Models\User;

class UsersController extends BaseController{
    public function getAddUserAction(){
        $responseMessage = null;
        return $this->renderHTML('addUser.twig',[
            'responseMessage'=>$responseMessage
        ]);

    }

    public function postAddUserAction($request){
        $responseMessage = null;
        
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();      
            $userValidator = v::key('email', v::stringType()->notEmpty())
                  ->key('password', v::stringType()->notEmpty());
            
            

            try {
                $userValidator->assert($postData);
                $postData = $request->getParsedBody();
                
                   
                $user = new User();
                $user->email = $postData['email'];
                $user->password = password_hash($postData['password'], PASSWORD_DEFAULT);
                $user->save();
                $responseMessage = 'Registered';
            } catch (\Exception $e) {
                $responseMessage = $e->getMessage();
            }
            
        }
        return $this->renderHTML('addUser.twig',[
            'responseMessage'=>$responseMessage
        ]);
    }
}