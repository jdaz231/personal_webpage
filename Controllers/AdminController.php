<?php
namespace Controllers;
require_once 'BaseController.php';
use Models\{Job,Project};

class AdminController extends BaseController{
    public function getIndex(){
        return $this->renderHTML('admin.twig');
    }
}
